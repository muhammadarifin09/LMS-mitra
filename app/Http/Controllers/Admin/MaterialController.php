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

class MaterialController extends Controller
{
    public function index(Kursus $kursus)
    {
        $materials = $kursus->materials()->orderBy('order')->get();
        return view('admin.kursus.materials.index', compact('kursus', 'materials'));
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
            // HAPUS: 'duration_video' => 'nullable|required_if:content_types,video|integer|min:1',
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

        // Validasi: pretest dan posttest tidak boleh bersamaan
        if (in_array('pretest', $request->content_types) && in_array('posttest', $request->content_types)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak dapat memilih pretest dan posttest bersamaan dalam satu materi.');
        }

        // Conditional validasi untuk file
        if (in_array('file', $request->content_types)) {
            $request->validate([
                'file_path' => 'required|array|min:1',
                'file_path.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:10240',
            ]);
        }

        // Conditional validasi untuk pretest
        if (in_array('pretest', $request->content_types)) {
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
        if (in_array('posttest', $request->content_types)) {
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
        if (in_array('file', $request->content_types) && $request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $filePaths[] = $file->store('materials', 'public');
            }
        }

        // Handle video upload ke Google Drive jika video_type = hosted
        $videoInfo = null;
        $videoDuration = 0; // Durasi akan di-set otomatis nanti
        
        if (in_array('video', $request->content_types) && $request->video_type === 'hosted' && $request->hasFile('video_file')) {
            try {
                $videoFile = $request->file('video_file');
                
                // Upload ke Google Drive
                $videoInfo = $this->uploadToGoogleDrive($videoFile);
                
                // Jika berhasil, dapatkan info video
                if ($videoInfo) {
                    // Simpan dalam format JSON
                    $videoInfo = json_encode($videoInfo);
                    
                    // Dapatkan durasi video (opsional - bisa diimplementasikan nanti)
                    // $videoDuration = $this->getVideoDuration($videoFile);
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
        if (in_array('pretest', $request->content_types)) {
            $type = 'pre_test';
        } elseif (in_array('posttest', $request->content_types)) {
            $type = 'post_test';
        }

        // Tentukan material_type berdasarkan content_types yang dipilih
        $materialType = 'theory';
        if (in_array('file', $request->content_types)) {
            $materialType = 'theory';
        } elseif (in_array('video', $request->content_types)) {
            $materialType = 'video';
        } elseif (in_array('pretest', $request->content_types) || in_array('posttest', $request->content_types)) {
            $materialType = 'quiz';
        }

        // Format soal pretest
        $soalPretest = null;
        if (in_array('pretest', $request->content_types) && !empty($request->pretest_soal)) {
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
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0)
                ];
            }
            $soalPretest = $soalFormatted;
        }

        // Format soal posttest
        $soalPosttest = null;
        if (in_array('posttest', $request->content_types) && !empty($request->posttest_soal)) {
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
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0)
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
            // Tambahkan untuk auto-detect duration
            'auto_detect_duration' => true,
        ];

        // Calculate video questions stats
        $hasVideoQuestions = false;
        $questionCount = 0;
        $totalVideoPoints = 0;

        if (in_array('video', $request->content_types) && $request->filled('video_questions')) {
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
            'duration' => $duration, // Sementara 0, bisa diupdate nanti
            'file_path' => !empty($filePaths) ? json_encode($filePaths) : null,
            'video_url' => $request->video_url ?? '',
            'video_type' => $request->video_type ?? 'external',
            'video_file' => $videoInfo, // Info Google Drive dalam JSON
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
            'learning_objectives' => json_encode($request->content_types),
            // Tambahkan flag untuk auto-duration
            'auto_duration' => true,
        ];

        // Tambahkan field durasi khusus jika ada (KECUALI duration_video)
        if (in_array('pretest', $request->content_types)) {
            $materialData['durasi_pretest'] = $request->durasi_pretest;
        }
        if (in_array('posttest', $request->content_types)) {
            $materialData['durasi_posttest'] = $request->durasi_posttest;
        }

        // Debug data sebelum disimpan
        Log::info('Material Data to be stored:', $materialData);

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
        return view('admin.kursus.materials.edit', compact('kursus', 'material'));
    }

    public function update(Request $request, Kursus $kursus, Materials $material)
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
            'video_file' => 'nullable|file|mimes:mp4,webm,avi,mov,wmv|max:102400',
            // HAPUS: 'duration_video' => 'nullable|required_if:content_types,video|integer|min:1',
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

        // Validasi: pretest dan posttest tidak boleh bersamaan
        if (in_array('pretest', $request->content_types) && in_array('posttest', $request->content_types)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak dapat memilih pretest dan posttest bersamaan dalam satu materi.');
        }

        // Conditional validasi untuk file
        if (in_array('file', $request->content_types)) {
            $request->validate([
                'file_path' => 'nullable|array',
                'file_path.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:10240',
            ]);
        }

        // Conditional validasi untuk pretest
        if (in_array('pretest', $request->content_types)) {
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
        if (in_array('posttest', $request->content_types)) {
            $request->validate([
                'durasi_posttest' => 'required|integer|min:1',
                'posttest_soal' => 'required|array|min:1',
                'posttest_soal.*.pertanyaan' => 'required|string',
                'posttest_soal.*.pilihan' => 'required|array|min:4|max:4',
                'posttest_soal.*.pilihan.*' => 'required|string',
                'posttest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
            ]);
        }

        // Handle multiple file uploads (tetap di local)
        $existingFiles = $material->file_path ? json_decode($material->file_path, true) : [];
        $newFilePaths = $existingFiles;

        if (in_array('file', $request->content_types) && $request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $newFilePaths[] = $file->store('materials', 'public');
            }
        } elseif (!in_array('file', $request->content_types)) {
            // Jika file tidak dipilih, hapus semua file yang ada
            foreach ($existingFiles as $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $newFilePaths = [];
        }

        // Handle video upload ke Google Drive jika video_type = hosted
        $videoInfo = $material->video_file;
        $videoDuration = $material->duration;
        
        if (in_array('video', $request->content_types) && $request->video_type === 'hosted' && $request->hasFile('video_file')) {
            try {
                // Delete old video file dari Google Drive jika ada
                if ($material->video_file) {
                    $oldVideoData = json_decode($material->video_file, true);
                    if ($oldVideoData && isset($oldVideoData['file_id'])) {
                        $this->deleteFromGoogleDrive($oldVideoData['file_id']);
                    }
                }
                
                $videoFile = $request->file('video_file');
                
                // Upload ke Google Drive
                $newVideoInfo = $this->uploadToGoogleDrive($videoFile);
                
                // Jika berhasil, dapatkan info video
                if ($newVideoInfo) {
                    // Simpan dalam format JSON
                    $videoInfo = json_encode($newVideoInfo);
                    
                    // Dapatkan durasi video (opsional)
                    // $videoDuration = $this->getVideoDuration($videoFile);
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

        // Tentukan type berdasarkan content_types
        $type = 'material';
        if (in_array('pretest', $request->content_types)) {
            $type = 'pre_test';
        } elseif (in_array('posttest', $request->content_types)) {
            $type = 'post_test';
        }

        // Tentukan material_type
        $materialType = 'theory';
        if (in_array('file', $request->content_types)) {
            $materialType = 'theory';
        } elseif (in_array('video', $request->content_types)) {
            $materialType = 'video';
        } elseif (in_array('pretest', $request->content_types) || in_array('posttest', $request->content_types)) {
            $materialType = 'quiz';
        }

        // Format soal pretest
        $soalPretest = null;
        if (in_array('pretest', $request->content_types) && !empty($request->pretest_soal)) {
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
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0)
                ];
            }
            $soalPretest = $soalFormatted;
        }

        // Format soal posttest
        $soalPosttest = null;
        if (in_array('posttest', $request->content_types) && !empty($request->posttest_soal)) {
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
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0)
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
            // Tambahkan untuk auto-detect duration
            'auto_detect_duration' => true,
        ];

        // Calculate video questions stats
        $hasVideoQuestions = false;
        $questionCount = 0;
        $totalVideoPoints = 0;

        if (in_array('video', $request->content_types) && $request->filled('video_questions')) {
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
            'learning_objectives' => json_encode($request->content_types),
            'auto_duration' => true,
        ];

        // Update field durasi khusus (KECUALI duration_video)
        if (in_array('pretest', $request->content_types)) {
            $materialData['durasi_pretest'] = $request->durasi_pretest;
        } else {
            $materialData['durasi_pretest'] = null;
            $materialData['soal_pretest'] = null;
        }

        if (in_array('posttest', $request->content_types)) {
            $materialData['durasi_posttest'] = $request->durasi_posttest;
        } else {
            $materialData['durasi_posttest'] = null;
            $materialData['soal_posttest'] = null;
        }

        // Jika bukan video, bersihkan data video
        if (!in_array('video', $request->content_types)) {
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

        // Debug data sebelum update
        Log::info('Material Data to be updated:', $materialData);

        try {
            $material->update($materialData);
            
            // Update video questions
            if ($hasVideoQuestions && $request->filled('video_questions')) {
                // Delete existing questions
                VideoQuestion::where('material_id', $material->id)->delete();
                
                // Create new questions
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
                // If no video questions, delete existing ones
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

            return redirect()->route('admin.kursus.materials.index', $kursus)
                            ->with('success', 'Material berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting material: ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', 'Gagal menghapus material: ' . $e->getMessage());
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
        
        // Load credentials dari file JSON
        $credentialsPath = storage_path('app/google-drive/credentials.json');
        
        if (!file_exists($credentialsPath)) {
            throw new \Exception('Credentials file not found at: ' . $credentialsPath);
        }
        
        $client->setAuthConfig($credentialsPath);
        $client->addScope(GoogleDrive::DRIVE_FILE);
        
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
        ]);
        
        $content = file_get_contents($file->getRealPath());
        
        $uploadedFile = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, name, webViewLink, webContentLink, size, mimeType, createdTime, modifiedTime',
            'supportsAllDrives' => true
        ]);
        
        // Buat file bisa diakses publik
        if (env('GOOGLE_DRIVE_MAKE_PUBLIC', true)) {
            try {
                $permission = new \Google\Service\Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                ]);
                
                $service->permissions->create($uploadedFile->id, $permission);
            } catch (\Exception $e) {
                Log::warning('Failed to make file public: ' . $e->getMessage());
            }
        }
        
        // Return informasi file
        return [
            'file_id' => $uploadedFile->id,
            'file_name' => $uploadedFile->name,
            'original_name' => $originalName,
            'web_view_link' => $uploadedFile->webViewLink,
            'web_content_link' => $uploadedFile->webContentLink,
            'size' => $uploadedFile->size,
            'mime_type' => $uploadedFile->mimeType,
            'created_time' => $uploadedFile->createdTime,
            'uploaded_at' => now()->toDateTimeString(),
        ];
        
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
        // Jangan throw error saat delete, karena mungkin file sudah tidak ada
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