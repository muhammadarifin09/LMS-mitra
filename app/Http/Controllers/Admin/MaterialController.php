<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Materials;
use App\Models\VideoQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\UserVideoProgress;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class MaterialController extends Controller
{
    public function index(Kursus $kursus)
    {
        $materials = $kursus->materials()->orderBy('order')->get();
        return view('admin.kursus.materials.index', compact('kursus', 'materials'));
    }

    // TAMBAHKAN METHOD UPDATE ORDER INI
    public function updateOrder(Request $request, Kursus $kursus)
    {
        try {
            $request->validate([
                'materials' => 'required|array',
                'materials.*.id' => 'required|exists:materials,id',
                'materials.*.order' => 'required|integer|min:1',
            ]);

            foreach ($request->materials as $item) {
                Materials::where('id', $item['id'])
                    ->where('course_id', $kursus->id)
                    ->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Urutan materi berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating material order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan materi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create(Kursus $kursus)
    {
        // Hitung urutan berikutnya
        $lastOrder = Materials::where('course_id', $kursus->id)->max('order');
        $nextOrder = $lastOrder ? $lastOrder + 1 : 1;
        
        // Hitung total materi
        $totalMaterials = Materials::where('course_id', $kursus->id)->count();

        return view('admin.kursus.materials.create', compact('kursus', 'nextOrder', 'totalMaterials'));
    }

    public function store(Request $request, Kursus $kursus)
    {
        // Validasi dasar - HAPUS duration_video
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'content_types' => 'required|array|min:1',
            'content_types.*' => 'in:file,video,pretest,posttest',
            'attendance_required' => 'boolean',
            'is_active' => 'boolean',
            
            // Video specific validations - HAPUS duration_video
            'video_type' => 'nullable|required_if:content_types,video|in:youtube,vimeo,hosted,external',
            'video_url' => 'nullable|required_if:video_type,external,youtube,vimeo|url',
            'video_file' => 'nullable|required_if:video_type,hosted|file|mimes:mp4,webm,avi,mov,wmv|max:102400',
            'allow_skip' => 'boolean',
            
            // Player config validations
            'disable_forward_seek' => 'boolean',
            'disable_backward_seek' => 'boolean',
            'disable_right_click' => 'boolean',
            'require_completion' => 'boolean',
            'min_watch_percentage' => 'nullable|integer|min:50|max:100',
            'auto_pause_on_question' => 'boolean',
            'require_question_completion' => 'boolean',
            
            // Video questions
            'video_questions' => 'nullable|array',
            'video_questions.*.time_in_seconds' => 'nullable|integer|min:0',
            'video_questions.*.question' => 'nullable|string|max:500',
            'video_questions.*.options' => 'nullable|array|min:2|max:4',
            'video_questions.*.options.*' => 'nullable|string|max:255',
            'video_questions.*.correct_option' => 'nullable|integer|min:0|max:3',
            'video_questions.*.points' => 'nullable|integer|min:1|max:10',
            'video_questions.*.explanation' => 'nullable|string',
            'video_questions.*.required_to_continue' => 'boolean',
        ]);

        // **LOGIKA VALIDASI KOMBINASI CONTENT TYPES**
        $contentTypes = $request->content_types;
        $errorMessage = $this->validateContentTypeCombination($contentTypes);
        
        if ($errorMessage) {
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        // Conditional validasi untuk file
        if (in_array('file', $contentTypes)) {
            $request->validate([
                'file_path' => 'nullable|array',
                'file_path.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:10240',
            ]);
        }

        // Conditional validasi untuk pretest
        if (in_array('pretest', $contentTypes)) {
            $request->validate([
                'durasi_pretest' => 'required|integer|min:1',
                'pretest_soal' => 'required|array|min:1',
                'pretest_soal.*.pertanyaan' => 'required|string',
                'pretest_soal.*.pilihan' => 'required|array|min:4|max:4',
                'pretest_soal.*.pilihan.*' => 'required|string',
                'pretest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
            ]);
        }

        // Conditional validasi untuk posttest
        if (in_array('posttest', $contentTypes)) {
            $request->validate([
                'durasi_posttest' => 'required|integer|min:1',
                'posttest_soal' => 'required|array|min:1',
                'posttest_soal.*.pertanyaan' => 'required|string',
                'posttest_soal.*.pilihan' => 'required|array|min:4|max:4',
                'posttest_soal.*.pilihan.*' => 'required|string',
                'posttest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
            ]);
        }

        // Handle multiple file uploads (tetap di local storage)
        $filePaths = [];
        if (in_array('file', $contentTypes) && $request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $filePaths[] = $file->store('materials', 'public');
            }
        }

        // Handle video upload ke Google Drive jika video_type = hosted
        $videoInfo = null;
        $videoDuration = 0;
        
        if (in_array('video', $contentTypes) && $request->video_type === 'hosted' && $request->hasFile('video_file')) {
            try {
                $videoFile = $request->file('video_file');
                $videoInfo = $this->uploadToGoogleDrive($videoFile);
                
                if ($videoInfo) {
                    $videoInfo = json_encode($videoInfo);
                } else {
                    throw new \Exception('Gagal upload video ke Google Drive');
                }
                
            } catch (\Exception $e) {
                Log::error('Error uploading video to Google Drive: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal mengupload video ke Google Drive: ' . $e->getMessage());
            }
        }

        // Hitung total durasi (video duration akan 0 untuk sementara)
        $duration = $videoDuration;

        // Tentukan type berdasarkan content_types
        $type = 'material';
        if (in_array('pretest', $contentTypes)) {
            $type = 'pre_test';
        } elseif (in_array('posttest', $contentTypes)) {
            $type = 'post_test';
        }

        // Tentukan material_type berdasarkan content_types yang dipilih
        $materialType = 'theory';
        if (in_array('file', $contentTypes)) {
            $materialType = 'theory';
        } elseif (in_array('video', $contentTypes)) {
            $materialType = 'video';
        } elseif (in_array('pretest', $contentTypes) || in_array('posttest', $contentTypes)) {
            $materialType = 'quiz';
        }

        // Format soal pretest dengan field baru (penjelasan dan poin)
        $soalPretest = null;
        if (in_array('pretest', $contentTypes) && !empty($request->pretest_soal)) {
            $soalFormatted = [];
            foreach ($request->pretest_soal as $index => $soal) {
                // Validasi bahwa semua pilihan terisi
                if (count(array_filter($soal['pilihan'])) < 4) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Semua pilihan jawaban untuk setiap soal harus diisi.');
                }

                $soalFormatted[] = [
                    'id' => $index + 1,
                    'pertanyaan' => $soal['pertanyaan'] ?? '',
                    'pilihan' => $soal['pilihan'] ?? [],
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0),
                    'poin' => (int)($soal['poin'] ?? 1)
                ];
            }
            $soalPretest = $soalFormatted;
        }

        // Format soal posttest dengan field baru (penjelasan dan poin)
        $soalPosttest = null;
        if (in_array('posttest', $contentTypes) && !empty($request->posttest_soal)) {
            $soalFormatted = [];
            foreach ($request->posttest_soal as $index => $soal) {
                // Validasi bahwa semua pilihan terisi
                if (count(array_filter($soal['pilihan'])) < 4) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Semua pilihan jawaban untuk setiap soal harus diisi.');
                }

                $soalFormatted[] = [
                    'id' => $index + 1,
                    'pertanyaan' => $soal['pertanyaan'] ?? '',
                    'pilihan' => $soal['pilihan'] ?? [],
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0),
                ];
            }
            $soalPosttest = $soalFormatted;
        }

        // Player config
        $playerConfig = [
            'allow_skip' => $request->boolean('allow_skip'),
            'disable_forward_seek' => $request->boolean('disable_forward_seek', true),
            'disable_backward_seek' => $request->boolean('disable_backward_seek', false),
            'disable_right_click' => $request->boolean('disable_right_click', true),
            'require_completion' => $request->boolean('require_completion', true),
            'min_watch_percentage' => $request->input('min_watch_percentage', 90),
            'auto_pause_on_question' => $request->boolean('auto_pause_on_question', true),
            'require_question_completion' => $request->boolean('require_question_completion', false),
            'auto_detect_duration' => true,
        ];

        // Calculate video questions stats
        $hasVideoQuestions = false;
        $questionCount = 0;
        $totalVideoPoints = 0;

        if (in_array('video', $contentTypes) && $request->filled('video_questions')) {
            $hasVideoQuestions = true;
            $questionCount = count(array_filter($request->video_questions, function($q) {
                return !empty($q['question']) && !empty($q['options']);
            }));
            
            foreach ($request->video_questions as $question) {
                if (!empty($question['question']) && !empty($question['options'])) {
                    $totalVideoPoints += $question['points'] ?? 1;
                }
            }
        }

        // Siapkan data untuk disimpan
        $materialData = [
            'course_id' => $kursus->id,
            'title' => $request->title,
            'description' => $request->description ?? '',
            'order' => $request->order,
            'type' => $type,
            'material_type' => $materialType,
            'duration' => $duration,
            'file_path' => !empty($filePaths) ? json_encode($filePaths) : null,
            'video_url' => $request->video_url ?? '',
            'video_type' => $request->video_type ?? 'external',
            'video_file' => $videoInfo,
            'allow_skip' => $request->boolean('allow_skip'),
            'player_config' => json_encode($playerConfig),
            'has_video_questions' => $hasVideoQuestions,
            'require_video_completion' => $request->boolean('require_completion', true),
            'question_count' => $questionCount,
            'total_video_points' => $totalVideoPoints,
            'is_active' => $request->boolean('is_active'),
            'attendance_required' => $request->boolean('attendance_required'),
            'soal_pretest' => $soalPretest,
            'soal_posttest' => $soalPosttest,
            'learning_objectives' => json_encode($contentTypes),
            'auto_duration' => true,
        ];

        // Tambahkan field durasi khusus
        if (in_array('pretest', $contentTypes)) {
            $materialData['durasi_pretest'] = $request->durasi_pretest;
        }
        if (in_array('posttest', $contentTypes)) {
            $materialData['durasi_posttest'] = $request->durasi_posttest;
        }

        try {
            // Create material
            $material = Materials::create($materialData);
            
            // Save video questions
            if ($hasVideoQuestions && $request->filled('video_questions')) {
                foreach ($request->video_questions as $index => $questionData) {
                    if (empty($questionData['question']) || empty($questionData['options'])) {
                        continue;
                    }
                    
                    VideoQuestion::create([
                        'material_id' => $material->id,
                        'order' => $index + 1,
                        'time_in_seconds' => $questionData['time_in_seconds'] ?? 0,
                        'question' => $questionData['question'],
                        'options' => json_encode($questionData['options']),
                        'correct_option' => $questionData['correct_option'] ?? 0,
                        'points' => $questionData['points'] ?? 1,
                        'explanation' => $questionData['explanation'] ?? null,
                        'required_to_continue' => $questionData['required_to_continue'] ?? true,
                    ]);
                }
            }
            
            return redirect()->route('admin.kursus.materials.index', $kursus)
                            ->with('success', 'Material berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing material: ' . $e->getMessage());
            
            // Hapus file yang sudah diupload jika ada error
            foreach ($filePaths as $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            
            // Hapus video file dari Google Drive jika ada error
            if ($videoInfo) {
                try {
                    $videoData = json_decode($videoInfo, true);
                    if (isset($videoData['file_id'])) {
                        $this->deleteFromGoogleDrive($videoData['file_id']);
                    }
                } catch (\Exception $deleteError) {
                    Log::error('Error deleting video from Google Drive: ' . $deleteError->getMessage());
                }
            }
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menyimpan material: ' . $e->getMessage());
        }
    }

    public function edit(Kursus $kursus, Materials $material)
    {
        $material->load('videoQuestions');
        // Decode content types untuk form edit
        $material->content_types_array = json_decode($material->learning_objectives ?? '[]', true);
        return view('admin.kursus.materials.edit', compact('kursus', 'material'));
    }

    public function update(Request $request, Kursus $kursus, Materials $material)
    {
        // Validasi dasar
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'content_types' => 'required|array|min:1',
            'content_types.*' => 'in:file,video,pretest,posttest',
            'attendance_required' => 'boolean',
            'is_active' => 'boolean',
            
            // Video specific validations
            'video_type' => 'nullable|required_if:content_types,video|in:youtube,vimeo,hosted,external',
            'video_url' => 'nullable|required_if:video_type,external,youtube,vimeo|url',
            'video_file' => 'nullable|file|mimes:mp4,webm,avi,mov,wmv|max:102400',
            'allow_skip' => 'boolean',
            
            // Player config validations
            'disable_forward_seek' => 'boolean',
            'disable_backward_seek' => 'boolean',
            'disable_right_click' => 'boolean',
            'require_completion' => 'boolean',
            'min_watch_percentage' => 'nullable|integer|min:50|max:100',
            'auto_pause_on_question' => 'boolean',
            'require_question_completion' => 'boolean',
            
            // Video questions
            'video_questions' => 'nullable|array',
            'video_questions.*.time_in_seconds' => 'nullable|integer|min:0',
            'video_questions.*.question' => 'nullable|string|max:500',
            'video_questions.*.options' => 'nullable|array|min:2|max:4',
            'video_questions.*.options.*' => 'nullable|string|max:255',
            'video_questions.*.correct_option' => 'nullable|integer|min:0|max:3',
            'video_questions.*.points' => 'nullable|integer|min:1|max:10',
            'video_questions.*.explanation' => 'nullable|string',
            'video_questions.*.required_to_continue' => 'boolean',
        ]);

        // **LOGIKA VALIDASI KOMBINASI CONTENT TYPES**
        $contentTypes = $request->content_types;
        $errorMessage = $this->validateContentTypeCombination($contentTypes);
        
        if ($errorMessage) {
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        // Validasi conditional berdasarkan jenis konten yang dipilih
        $validationErrors = $this->validateConditionalFields($request, $contentTypes);
        if ($validationErrors) {
            return $validationErrors;
        }

        // Handle multiple file uploads
        $existingFiles = $material->file_path ? json_decode($material->file_path, true) : [];
        $newFilePaths = $existingFiles;

        if (in_array('file', $contentTypes) && $request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $newFilePaths[] = $file->store('materials', 'public');
            }
        } elseif (!in_array('file', $contentTypes)) {
            // Jika file tidak dipilih, hapus semua file yang ada
            foreach ($existingFiles as $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $newFilePaths = [];
        }

        // Handle video upload ke Google Drive
        $videoInfo = $material->video_file;
        $videoDuration = $material->duration;
        
        if (in_array('video', $contentTypes) && $request->video_type === 'hosted' && $request->hasFile('video_file')) {
            try {
                // Delete old video file dari Google Drive jika ada
                if ($material->video_file) {
                    $oldVideoData = json_decode($material->video_file, true);
                    if ($oldVideoData && isset($oldVideoData['file_id'])) {
                        $this->deleteFromGoogleDrive($oldVideoData['file_id']);
                    }
                }
                
                $videoFile = $request->file('video_file');
                $newVideoInfo = $this->uploadToGoogleDrive($videoFile);
                
                if ($newVideoInfo) {
                    $videoInfo = json_encode($newVideoInfo);
                } else {
                    throw new \Exception('Gagal upload video ke Google Drive');
                }
                
            } catch (\Exception $e) {
                Log::error('Error uploading video to Google Drive: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal mengupload video ke Google Drive: ' . $e->getMessage());
            }
        }

        // Hitung total durasi
        $duration = $videoDuration;

        // Tentukan type dan material_type
        list($type, $materialType) = $this->determineMaterialTypes($contentTypes);

        // Format soal pretest
        $soalPretest = null;
        if (in_array('pretest', $contentTypes) && !empty($request->pretest_soal)) {
            $soalFormatted = $this->formatSoal($request->pretest_soal, 'pretest');
            if (is_string($soalFormatted)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $soalFormatted);
            }
            $soalPretest = $soalFormatted;
        }

        // Format soal posttest
        $soalPosttest = null;
        if (in_array('posttest', $contentTypes) && !empty($request->posttest_soal)) {
            $soalFormatted = $this->formatSoal($request->posttest_soal, 'posttest');
            if (is_string($soalFormatted)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $soalFormatted);
            }
            $soalPosttest = $soalFormatted;
        }

        // Player config
        $playerConfig = [
            'allow_skip' => $request->boolean('allow_skip'),
            'disable_forward_seek' => $request->boolean('disable_forward_seek', true),
            'disable_backward_seek' => $request->boolean('disable_backward_seek', false),
            'disable_right_click' => $request->boolean('disable_right_click', true),
            'require_completion' => $request->boolean('require_completion', true),
            'min_watch_percentage' => $request->input('min_watch_percentage', 90),
            'auto_pause_on_question' => $request->boolean('auto_pause_on_question', true),
            'require_question_completion' => $request->boolean('require_question_completion', false),
            'auto_detect_duration' => true,
        ];

        // Calculate video questions stats
        list($hasVideoQuestions, $questionCount, $totalVideoPoints) = $this->calculateVideoQuestionStats($request, $contentTypes);

        // Update data
        $materialData = [
            'title' => $request->title,
            'description' => $request->description ?? '',
            'order' => $request->order,
            'type' => $type,
            'material_type' => $materialType,
            'duration' => $duration,
            'file_path' => !empty($newFilePaths) ? json_encode($newFilePaths) : null,
            'video_url' => $request->video_url ?? '',
            'video_type' => $request->video_type ?? $material->video_type,
            'video_file' => $videoInfo,
            'allow_skip' => $request->boolean('allow_skip'),
            'player_config' => json_encode($playerConfig),
            'has_video_questions' => $hasVideoQuestions,
            'require_video_completion' => $request->boolean('require_completion', true),
            'question_count' => $questionCount,
            'total_video_points' => $totalVideoPoints,
            'is_active' => $request->boolean('is_active'),
            'attendance_required' => $request->boolean('attendance_required'),
            'soal_pretest' => $soalPretest,
            'soal_posttest' => $soalPosttest,
            'learning_objectives' => json_encode($contentTypes),
            'auto_duration' => true,
        ];

        // Update field durasi khusus
        if (in_array('pretest', $contentTypes)) {
            $materialData['durasi_pretest'] = $request->durasi_pretest;
        } else {
            $materialData['durasi_pretest'] = null;
            $materialData['soal_pretest'] = null;
        }

        if (in_array('posttest', $contentTypes)) {
            $materialData['durasi_posttest'] = $request->durasi_posttest;
        } else {
            $materialData['durasi_posttest'] = null;
            $materialData['soal_posttest'] = null;
        }

        // Jika bukan video, bersihkan data video
        if (!in_array('video', $contentTypes)) {
            $materialData['video_url'] = null;
            $materialData['video_type'] = 'external';
            $materialData['video_file'] = null;
            $materialData['player_config'] = null;
            $materialData['has_video_questions'] = false;
            $materialData['question_count'] = 0;
            $materialData['total_video_points'] = 0;
            
            // Hapus file video dari Google Drive jika ada
            if ($material->video_file) {
                try {
                    $oldVideoData = json_decode($material->video_file, true);
                    if ($oldVideoData && isset($oldVideoData['file_id'])) {
                        $this->deleteFromGoogleDrive($oldVideoData['file_id']);
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting video from Google Drive: ' . $e->getMessage());
                }
            }
        }

        try {
            $material->update($materialData);
            
            // Update video questions
            if ($hasVideoQuestions && $request->filled('video_questions')) {
                VideoQuestion::where('material_id', $material->id)->delete();
                
                foreach ($request->video_questions as $index => $questionData) {
                    if (empty($questionData['question']) || empty($questionData['options'])) {
                        continue;
                    }
                    
                    VideoQuestion::create([
                        'material_id' => $material->id,
                        'order' => $index + 1,
                        'time_in_seconds' => $questionData['time_in_seconds'] ?? 0,
                        'question' => $questionData['question'],
                        'options' => json_encode($questionData['options']),
                        'correct_option' => $questionData['correct_option'] ?? 0,
                        'points' => $questionData['points'] ?? 1,
                        'explanation' => $questionData['explanation'] ?? null,
                        'required_to_continue' => $questionData['required_to_continue'] ?? true,
                    ]);
                }
            } else {
                VideoQuestion::where('material_id', $material->id)->delete();
            }

            return redirect()->route('admin.kursus.materials.index', $kursus)
                            ->with('success', 'Material berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating material: ' . $e->getMessage());
            
            // Hapus file baru yang sudah diupload jika ada error
            if ($request->hasFile('file_path')) {
                foreach ($request->file('file_path') as $file) {
                    $filePath = $file->store('materials', 'public');
                    Storage::disk('public')->delete($filePath);
                }
            }
            
            // Hapus video file baru dari Google Drive jika ada error
            if ($request->hasFile('video_file') && isset($newVideoInfo)) {
                try {
                    if (isset($newVideoInfo['file_id'])) {
                        $this->deleteFromGoogleDrive($newVideoInfo['file_id']);
                    }
                } catch (\Exception $deleteError) {
                    Log::error('Error deleting new video from Google Drive: ' . $deleteError->getMessage());
                }
            }
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal memperbarui material: ' . $e->getMessage());
        }
    }

    public function destroy(Kursus $kursus, Materials $material)
{
    try {
        // Simpan order sebelum dihapus untuk reorder
        $deletedOrder = $material->order;
        
        // Delete files (tetap di local)
        if ($material->file_path) {
            $files = json_decode($material->file_path, true);
            if (is_array($files)) {
                foreach ($files as $filePath) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }

        // Delete video file dari Google Drive
        if ($material->video_file) {
            try {
                $videoData = json_decode($material->video_file, true);
                if ($videoData && isset($videoData['file_id'])) {
                    $this->deleteFromGoogleDrive($videoData['file_id']);
                }
            } catch (\Exception $e) {
                Log::error('Error deleting video from Google Drive: ' . $e->getMessage());
            }
        }

        // Delete video questions
        VideoQuestion::where('material_id', $material->id)->delete();
        
        // Delete video progress
        UserVideoProgress::where('material_id', $material->id)->delete();

        $material->delete();

        // Reorder secara manual (backup jika event deleted tidak jalan)
        $this->reorderMaterials($kursus->id);

        return redirect()->route('admin.kursus.materials.index', $kursus)
                        ->with('success', 'Material berhasil dihapus!');
    } catch (\Exception $e) {
        Log::error('Error deleting material: ' . $e->getMessage());
        
        return redirect()->back()
                        ->with('error', 'Gagal menghapus material: ' . $e->getMessage());
    }
}

// Tambahkan method helper untuk reorder
private function reorderMaterials($courseId)
{
    $materials = Materials::where('course_id', $courseId)
        ->orderBy('order')
        ->orderBy('created_at')
        ->get();
    
    $order = 1;
    foreach ($materials as $material) {
        $material->order = $order;
        $material->saveQuietly(); // saveQuietly untuk menghindari event updating
        $order++;
    }
}

    public function updateStatus(Request $request, Kursus $kursus, Materials $material)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        try {
            $material->update(['is_active' => $request->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status material berhasil diperbarui!',
                'new_status' => $material->is_active ? 'Aktif' : 'Nonaktif'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating material status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status material: ' . $e->getMessage()
            ], 500);
        }
    }

    // NEW METHODS FOR VIDEO MANAGEMENT
    
    public function videoQuestions(Kursus $kursus, Materials $material)
    {
        $material->load('videoQuestions');
        return view('admin.kursus.materials.video-questions', compact('kursus', 'material'));
    }

    public function videoPreview(Kursus $kursus, Materials $material)
    {
        $material->load('videoQuestions');
        
        // Dapatkan URL video dari Google Drive
        $videoUrl = $this->getVideoUrl($material);
        
        $videoData = [
            'type' => $material->video_type,
            'url' => $videoUrl ?: $material->video_url,
            'video_id' => $this->getVideoId($material),
            'embed_url' => $this->getVideoEmbedUrl($material),
            'config' => $material->player_config,
        ];
        
        return view('admin.kursus.materials.video-preview', compact('kursus', 'material', 'videoData'));
    }

    public function videoStats(Kursus $kursus, Materials $material)
    {
        $stats = [
            'total_views' => $material->total_views,
            'total_completions' => $material->total_completions,
            'completion_rate' => $material->total_views > 0 ? 
                round(($material->total_completions / $material->total_views) * 100, 2) : 0,
            'avg_completion_time' => $material->avg_completion_time,
            'question_stats' => [
                'total_questions' => $material->question_count,
                'total_points' => $material->total_video_points,
                'avg_points_earned' => UserVideoProgress::where('material_id', $material->id)
                    ->where('total_points_earned', '>', 0)
                    ->avg('total_points_earned') ?? 0,
            ],
            'progress_data' => UserVideoProgress::where('material_id', $material->id)
                ->selectRaw('progress_percentage, COUNT(*) as count')
                ->groupBy('progress_percentage')
                ->orderBy('progress_percentage')
                ->get(),
        ];
        
        return view('admin.kursus.materials.video-stats', compact('kursus', 'material', 'stats'));
    }

    private function validateContentTypeCombination($contentTypes)
    {
        // Jika memilih pretest
        if (in_array('pretest', $contentTypes)) {
            if (count($contentTypes) > 1) {
                return 'Jika memilih pretest, tidak bisa memilih konten lain.';
            }
        }
        
        // Jika memilih posttest
        if (in_array('posttest', $contentTypes)) {
            if (count($contentTypes) > 1) {
                return 'Jika memilih posttest, tidak bisa memilih konten lain.';
            }
        }
        
        // Jika memilih file (PDF/PPT)
        if (in_array('file', $contentTypes)) {
            $allowedWithFile = ['video']; // File bisa dikombinasikan dengan video
            foreach ($contentTypes as $type) {
                if ($type !== 'file' && !in_array($type, $allowedWithFile)) {
                    if (in_array($type, ['pretest', 'posttest'])) {
                        return 'File tidak bisa dikombinasikan dengan ' . ($type === 'pretest' ? 'pretest' : 'posttest') . '.';
                    }
                }
            }
            
            // Cek apakah kombinasi file dan video valid
            if (count($contentTypes) > 2 || (count($contentTypes) === 2 && !in_array('video', $contentTypes))) {
                return 'File hanya bisa dikombinasikan dengan video.';
            }
        }
        
        // Jika memilih video
        if (in_array('video', $contentTypes)) {
            $allowedWithVideo = ['file']; // Video bisa dikombinasikan dengan file
            foreach ($contentTypes as $type) {
                if ($type !== 'video' && !in_array($type, $allowedWithVideo)) {
                    if (in_array($type, ['pretest', 'posttest'])) {
                        return 'Video tidak bisa dikombinasikan dengan ' . ($type === 'pretest' ? 'pretest' : 'posttest') . '.';
                    }
                }
            }
            
            // Cek apakah kombinasi video valid
            if (count($contentTypes) > 2 || (count($contentTypes) === 2 && !in_array('file', $contentTypes))) {
                return 'Video hanya bisa dikombinasikan dengan file.';
            }
        }

        return null;
    }

    private function validateConditionalFields($request, $contentTypes)
    {
        // Conditional validasi untuk file
        if (in_array('file', $contentTypes)) {
            if (!$request->hasFile('file_path') && empty($request->input('existing_files', []))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Harap upload file untuk konten tipe file.');
            }
            
            $request->validate([
                'file_path' => 'nullable|array',
                'file_path.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:10240',
            ]);
        }

        // Conditional validasi untuk pretest
        if (in_array('pretest', $contentTypes)) {
            $request->validate([
                'durasi_pretest' => 'required|integer|min:1',
                'pretest_soal' => 'required|array|min:1',
                'pretest_soal.*.pertanyaan' => 'required|string',
                'pretest_soal.*.pilihan' => 'required|array|min:4|max:4',
                'pretest_soal.*.pilihan.*' => 'required|string',
                'pretest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
            ]);
        }

        // Conditional validasi untuk posttest
        if (in_array('posttest', $contentTypes)) {
            $request->validate([
                'durasi_posttest' => 'required|integer|min:1',
                'posttest_soal' => 'required|array|min:1',
                'posttest_soal.*.pertanyaan' => 'required|string',
                'posttest_soal.*.pilihan' => 'required|array|min:4|max:4',
                'posttest_soal.*.pilihan.*' => 'required|string',
                'posttest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
            ]);
        }

        return null;
    }

    private function determineMaterialTypes($contentTypes)
    {
        $type = 'material';
        $materialType = 'theory';
        
        if (in_array('pretest', $contentTypes)) {
            $type = 'pre_test';
            $materialType = 'quiz';
        } elseif (in_array('posttest', $contentTypes)) {
            $type = 'post_test';
            $materialType = 'quiz';
        } elseif (in_array('file', $contentTypes) && in_array('video', $contentTypes)) {
            $materialType = 'mixed';
        } elseif (in_array('file', $contentTypes)) {
            $materialType = 'theory';
        } elseif (in_array('video', $contentTypes)) {
            $materialType = 'video';
        }
        
        return [$type, $materialType];
    }

    private function formatSoal($soalData, $type)
    {
        $soalFormatted = [];
        foreach ($soalData as $index => $soal) {
            // Validasi bahwa semua pilihan terisi
            if (count(array_filter($soal['pilihan'])) < 4) {
                return 'Semua pilihan jawaban untuk setiap soal ' . $type . ' harus diisi.';
            }

            $soalFormatted[] = [
                'id' => $index + 1,
                'pertanyaan' => $soal['pertanyaan'] ?? '',
                'pilihan' => $soal['pilihan'] ?? [],
                'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0)
            ];
        }
        return $soalFormatted;
    }

    private function calculateVideoQuestionStats($request, $contentTypes)
    {
        $hasVideoQuestions = false;
        $questionCount = 0;
        $totalVideoPoints = 0;

        if (in_array('video', $contentTypes) && $request->filled('video_questions')) {
            $hasVideoQuestions = true;
            $questionCount = count(array_filter($request->video_questions, function($q) {
                return !empty($q['question']) && !empty($q['options']);
            }));
            
            foreach ($request->video_questions as $question) {
                if (!empty($question['question']) && !empty($question['options'])) {
                    $totalVideoPoints += $question['points'] ?? 1;
                }
            }
        }
        
        return [$hasVideoQuestions, $questionCount, $totalVideoPoints];
    }

    public function updateVideoQuestions(Request $request, Kursus $kursus, Materials $material)
    {
        $request->validate([
            'questions' => 'nullable|array',
            'questions.*.time_in_seconds' => 'required|integer|min:0',
            'questions.*.question' => 'required|string|max:500',
            'questions.*.options' => 'required|array|min:2|max:4',
            'questions.*.options.*' => 'required|string|max:255',
            'questions.*.correct_option' => 'required|integer|min:0|max:3',
            'questions.*.points' => 'required|integer|min:1|max:10',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.required_to_continue' => 'boolean',
        ]);

        try {
            // Delete existing questions
            VideoQuestion::where('material_id', $material->id)->delete();
            
            // Calculate stats
            $questionCount = count($request->questions);
            $totalVideoPoints = 0;
            
            // Create new questions
            if (!empty($request->questions)) {
                foreach ($request->questions as $index => $questionData) {
                    $totalVideoPoints += $questionData['points'];
                    
                    VideoQuestion::create([
                        'material_id' => $material->id,
                        'order' => $index + 1,
                        'time_in_seconds' => $questionData['time_in_seconds'],
                        'question' => $questionData['question'],
                        'options' => json_encode($questionData['options']),
                        'correct_option' => $questionData['correct_option'],
                        'points' => $questionData['points'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'required_to_continue' => $questionData['required_to_continue'] ?? true,
                    ]);
                }
            }
            
            // Update material stats
            $material->update([
                'has_video_questions' => $questionCount > 0,
                'question_count' => $questionCount,
                'total_video_points' => $totalVideoPoints,
            ]);
            
            return redirect()->back()
                            ->with('success', 'Pertanyaan video berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating video questions: ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui pertanyaan video: ' . $e->getMessage());
        }
    }

    public function updatePlayerConfig(Request $request, Kursus $kursus, Materials $material)
    {
        $request->validate([
            'allow_skip' => 'boolean',
            'disable_forward_seek' => 'boolean',
            'disable_backward_seek' => 'boolean',
            'disable_right_click' => 'boolean',
            'require_completion' => 'boolean',
            'min_watch_percentage' => 'integer|min:50|max:100',
            'auto_pause_on_question' => 'boolean',
            'require_question_completion' => 'boolean',
        ]);

        try {
            $playerConfig = [
                'allow_skip' => $request->boolean('allow_skip'),
                'disable_forward_seek' => $request->boolean('disable_forward_seek', true),
                'disable_backward_seek' => $request->boolean('disable_backward_seek', false),
                'disable_right_click' => $request->boolean('disable_right_click', true),
                'require_completion' => $request->boolean('require_completion', true),
                'min_watch_percentage' => $request->input('min_watch_percentage', 90),
                'auto_pause_on_question' => $request->boolean('auto_pause_on_question', true),
                'require_question_completion' => $request->boolean('require_question_completion', false),
            ];
            
            $material->update([
                'allow_skip' => $request->boolean('allow_skip'),
                'player_config' => json_encode($playerConfig),
                'require_video_completion' => $request->boolean('require_completion', true),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan player berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating player config: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan player: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // HELPER METHODS FOR GOOGLE DRIVE INTEGRATION
    // ============================================

    /**
     * Upload file to Google Drive
     */
    // GANTI method uploadToGoogleDrive dengan ini:
    private function uploadToGoogleDrive($file)
    {
        try {
            Log::info('Starting Google Drive upload...');
            
            $client = new GoogleClient();
            
            // Load credentials
            $credentialsPath = storage_path('app/google-drive/credentials.json');
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Credentials file not found');
            }
            
            $client->setAuthConfig($credentialsPath);
            $client->addScope(GoogleDrive::DRIVE_FILE);
            $client->addScope(GoogleDrive::DRIVE_READONLY);
            
            // Set subject untuk service account
            $serviceAccount = json_decode(file_get_contents($credentialsPath), true);
            $client->setSubject($serviceAccount['client_email']);
            
            $service = new GoogleDrive($client);
            
            // Generate unique filename
            $filename = 'video_' . Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            
            // Upload file ke Google Drive
            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $filename,
                'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')],
                'description' => 'Video materi pembelajaran - ' . $originalName,
                'mimeType' => $file->getMimeType(),
            ]);
            
            $content = file_get_contents($file->getRealPath());
            
            $uploadedFile = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $file->getMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id, name, webViewLink, webContentLink, size, mimeType, thumbnailLink',
                'supportsAllDrives' => true
            ]);
            
            // Buat file bisa diakses publik
            try {
                $permission = new \Google\Service\Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                    'allowFileDiscovery' => false,
                ]);
                
                $service->permissions->create($uploadedFile->id, $permission);
                
                // Buat embed link khusus
                $embedLink = 'https://drive.google.com/file/d/' . $uploadedFile->id . '/preview';
                
                // Get thumbnail link
                $thumbnailLink = $uploadedFile->thumbnailLink ?? null;
                
                // Return informasi file dengan embed link
                return [
                    'file_id' => $uploadedFile->id,
                    'file_name' => $uploadedFile->name,
                    'original_name' => $originalName,
                    'web_view_link' => $uploadedFile->webViewLink,
                    'web_content_link' => $uploadedFile->webContentLink,
                    'embed_link' => $embedLink,
                    'thumbnail_link' => $thumbnailLink,
                    'size' => $uploadedFile->size,
                    'mime_type' => $uploadedFile->mimeType,
                    'uploaded_at' => now()->toDateTimeString(),
                    'direct_play' => true,
                ];
                
            } catch (\Exception $e) {
                Log::warning('Failed to make file public: ' . $e->getMessage());
                
                // Tetap return data tanpa permission
                return [
                    'file_id' => $uploadedFile->id,
                    'file_name' => $uploadedFile->name,
                    'original_name' => $originalName,
                    'web_view_link' => $uploadedFile->webViewLink,
                    'embed_link' => 'https://drive.google.com/file/d/' . $uploadedFile->id . '/preview',
                    'size' => $uploadedFile->size,
                    'mime_type' => $uploadedFile->mimeType,
                    'uploaded_at' => now()->toDateTimeString(),
                    'direct_play' => true,
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error in uploadToGoogleDrive: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

// Juga perbaiki deleteFromGoogleDrive:
private function deleteFromGoogleDrive($fileId)
    {
        try {
            $client = new GoogleClient();
            
            $credentialsPath = storage_path('app/google-drive/credentials.json');
            $client->setAuthConfig($credentialsPath);
            $client->addScope(GoogleDrive::DRIVE_FILE);
            
            $serviceAccount = json_decode(file_get_contents($credentialsPath), true);
            $client->setSubject($serviceAccount['client_email']);
            
            $service = new GoogleDrive($client);
            $service->files->delete($fileId);
            
            Log::info('File deleted from Google Drive: ' . $fileId);
            
        } catch (\Exception $e) {
            Log::error('Error deleting from Google Drive: ' . $e->getMessage());
        }
    }


    /**
     * Get video URL from Google Drive
     */
    private function getVideoUrl($material)
    {
        if ($material->video_type === 'hosted' && $material->video_file) {
            try {
                $videoData = json_decode($material->video_file, true);
                if ($videoData && isset($videoData['web_view_link'])) {
                    return $videoData['web_view_link'];
                }
            } catch (\Exception $e) {
                Log::error('Error getting video URL: ' . $e->getMessage());
            }
        }
        
        return $material->video_url;
    }

    /**
     * Get video ID for embedding
     */
    private function getVideoId($material)
    {
        if ($material->video_type === 'hosted' && $material->video_file) {
            try {
                $videoData = json_decode($material->video_file, true);
                if ($videoData && isset($videoData['file_id'])) {
                    return $videoData['file_id'];
                }
            } catch (\Exception $e) {
                Log::error('Error getting video ID: ' . $e->getMessage());
            }
        }
        
        // Untuk YouTube/Vimeo, extract ID dari URL
        if ($material->video_type === 'youtube' || $material->video_type === 'vimeo') {
            return $this->extractVideoId($material->video_url, $material->video_type);
        }
        
        return null;
    }

    /**
     * Get video embed URL
     */
    private function getVideoEmbedUrl($material)
    {
        $videoId = $this->getVideoId($material);
        
        if ($material->video_type === 'youtube' && $videoId) {
            return 'https://www.youtube.com/embed/' . $videoId;
        } elseif ($material->video_type === 'vimeo' && $videoId) {
            return 'https://player.vimeo.com/video/' . $videoId;
        } elseif ($material->video_type === 'hosted' && $material->video_file) {
            $videoData = json_decode($material->video_file, true);
            if ($videoData && isset($videoData['web_view_link'])) {
                // Untuk Google Drive, ubah view link menjadi embed
                return str_replace('/view', '/preview', $videoData['web_view_link']);
            }
        }
        
        return null;
    }

    /**
     * Extract video ID from URL
     */
    private function extractVideoId($url, $type)
    {
        if ($type === 'youtube') {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
            return $matches[1] ?? null;
        } elseif ($type === 'vimeo') {
            preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $matches);
            return $matches[1] ?? null;
        }
        
        return null;
    }
    
    /**
     * Import soal dari Excel
     */
    public function importSoal(Request $request, Kursus $kursus)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // 5MB max
            'type' => 'required|in:pretest,posttest',
            'preview_only' => 'nullable|boolean'
        ]);

        try {
            $import = new SoalImport();
            
            if ($request->preview_only) {
                // Untuk preview, baca hanya 50 baris pertama
                $data = Excel::toCollection($import, $request->file('file'))->first()->take(50);
            } else {
                $data = Excel::toCollection($import, $request->file('file'))->first();
            }
            
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel kosong atau format tidak sesuai'
                ], 400);
            }
            
            $soalData = $this->processExcelData($data, $request->type);
            
            if (empty($soalData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data soal yang valid ditemukan. Pastikan format sesuai template.'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimport',
                'data' => $soalData,
                'count' => count($soalData)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error importing soal: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download template Excel
     */
    // Di controller Anda (contoh: MaterialController.php)
// Di App\Http\Controllers\Admin\MaterialController.php

/**
 * Download template Excel untuk soal
 */
public function downloadTemplate()
{
    try {
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header TANPA POIN
        $headers = ['PERTANYAAN', 'PILIHAN_A', 'PILIHAN_B', 'PILIHAN_C', 'PILIHAN_D', 'JAWABAN_BENAR'];
        
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index);
            $sheet->setCellValue($col . '1', $header);
            
            // Style header
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3C72']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
        }
        
        // Contoh data TANPA POIN
        $examples = [
            [
                'Apa ibu kota Indonesia?',
                'Jakarta',
                'Bandung',
                'Surabaya',
                'Yogyakarta',
                'A'
            ],
            [
                'Siapa presiden pertama Indonesia?',
                'Soekarno',
                'Soeharto',
                'BJ Habibie',
                'Gus Dur',
                'A'
            ],
            [
                'Tanggal berapa Indonesia merdeka?',
                '17 Agustus 1945',
                '27 Desember 1949',
                '1 Juni 1945',
                '10 November 1945',
                'A'
            ]
        ];
        
        // Tambahkan contoh data
        $row = 2;
        foreach ($examples as $example) {
            foreach ($example as $index => $value) {
                $col = chr(65 + $index);
                $sheet->setCellValue($col . $row, $value);
            }
            $row++;
        }
        
        // Set width columns
        $sheet->getColumnDimension('A')->setWidth(40); // Pertanyaan
        $sheet->getColumnDimension('B')->setWidth(20); // Pilihan A
        $sheet->getColumnDimension('C')->setWidth(20); // Pilihan B
        $sheet->getColumnDimension('D')->setWidth(20); // Pilihan C
        $sheet->getColumnDimension('E')->setWidth(20); // Pilihan D
        $sheet->getColumnDimension('F')->setWidth(15); // Jawaban Benar
        
        // Data validation untuk kolom jawaban
        $validation = $sheet->getCell('F2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setFormula1('"A,B,C,D"');
        $validation->setShowDropDown(true);
        $validation->setAllowBlank(false);
        
        // Terapkan ke semua baris contoh
        for ($i = 2; $i <= 4; $i++) {
            $sheet->getCell('F' . $i)->setDataValidation(clone $validation);
        }
        
        // Simpan ke temporary file
        $filename = 'template-soal-' . date('YmdHis') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);
        
        // Pastikan directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        
        // Download file
        return response()->download($tempPath, 'template-soal.xlsx')->deleteFileAfterSend(true);
        
    } catch (\Exception $e) {
        Log::error('Error creating template: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat template: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Process data dari Excel
     */
    private function processExcelData($rows, string $type): array
{
    $soalData = [];
    
    foreach ($rows as $index => $row) {
        // Skip jika baris kosong
        if (empty($row) || !isset($row['pertanyaan']) || empty(trim($row['pertanyaan']))) {
            continue;
        }
        
        // Format data soal TANPA POIN
        $soal = [
            'id' => count($soalData) + 1,
            'pertanyaan' => trim($row['pertanyaan']),
            'pilihan' => [
                isset($row['pilihan_a']) ? trim($row['pilihan_a']) : '',
                isset($row['pilihan_b']) ? trim($row['pilihan_b']) : '',
                isset($row['pilihan_c']) ? trim($row['pilihan_c']) : '',
                isset($row['pilihan_d']) ? trim($row['pilihan_d']) : '',
            ],
            'jawaban_benar' => $this->convertJawabanToIndex($row['jawaban_benar'] ?? 'A'),
            'row_number' => $index + 2
        ];
        
        // Validasi: pastikan ada minimal 2 pilihan yang tidak kosong
        $validPilihan = array_filter($soal['pilihan'], function($pilihan) {
            return !empty(trim($pilihan));
        });
        
        if (count($validPilihan) < 2) {
            \Log::warning("Soal pada baris {$soal['row_number']} diabaikan: kurang dari 2 pilihan yang valid");
            continue;
        }
        
        $soalData[] = $soal;
        
        // Batasi maksimal 500 soal untuk performance
        if (count($soalData) >= 500) {
            \Log::info("Mencapai batas maksimal 500 soal, menghentikan proses import");
            break;
        }
    }
    
    \Log::info("Berhasil memproses " . count($soalData) . " soal dari Excel");
    return $soalData;
}

    /**
     * Convert jawaban (A/B/C/D) ke index (0/1/2/3)
     */
    private function convertJawabanToIndex($jawaban): int
    {
        if (empty($jawaban)) {
            return 0;
        }
        
        $jawaban = strtoupper(trim($jawaban));
        
        switch ($jawaban) {
            case 'A': return 0;
            case 'B': return 1;
            case 'C': return 2;
            case 'D': return 3;
            default: return 0;
        }
    }

    /**
     * Get video duration (optional - untuk implementasi nanti)
     */
    private function getVideoDuration($file)
    {
        // Implementasi untuk mendapatkan durasi video
        // Bisa menggunakan FFmpeg atau library PHP lainnya
        // Return dalam detik
        return 0; // Default 0
    }
}