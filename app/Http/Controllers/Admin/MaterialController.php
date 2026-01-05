<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialProgress;
use App\Models\Kursus;
use App\Models\Materials;
use App\Models\VideoQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // TAMBAH INI
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
    // TAMBAH INI DI AWAL: Deteksi apakah request AJAX
    $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest';
    
    Log::info('Store Request Debug', [
        'isAjax' => $isAjax,
        'content_types' => $request->content_types ?? []
    ]);

    try {
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
            'video_type' => 'nullable|required_if:content_types,video|in:youtube,hosted,local',
            'video_url' => 'nullable|required_if:video_type,youtube|url',
            'video_file' => 'nullable|required_if:video_type,hosted,local|file|mimes:mp4,webm,avi,mov,wmv,mkv|max:102400',
            'allow_skip' => 'boolean',
            
            // Player config validations
            'disable_forward_seek' => 'boolean',
            'disable_backward_seek' => 'boolean',
            'disable_right_click' => 'boolean',
            'require_completion' => 'boolean',
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
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
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

        // Handle multiple file uploads
        $filePaths = [];
        if (in_array('file', $contentTypes) && $request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $filePaths[] = $file->store('materials', 'public');
            }
        }

        // ============================================
        // PERBAIKAN BESAR: VIDEO HANDLING SECTION
        // ============================================
        
        $videoData = null;
        $videoDuration = 0;
        $videoFilePath = null;

        if (in_array('video', $contentTypes)) {
            try {
                $videoType = $request->video_type;
                Log::info('Processing video upload', [
                    'video_type' => $videoType,
                    'has_file' => $request->hasFile('video_file')
                ]);
                
                // YouTube videos
                if ($videoType === 'youtube') {
                    // Validasi URL YouTube
                    if (!$this->isValidYouTubeUrl($request->video_url)) {
                        if ($isAjax) {
                            return response()->json([
                                'success' => false,
                                'message' => 'URL YouTube tidak valid.'
                            ], 422);
                        }
                        
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'URL YouTube tidak valid.');
                    }
                    
                    $videoData = null; // YouTube tidak menyimpan video_data
                    Log::info('YouTube video configured', ['url' => $request->video_url]);
                }
                
                // Google Drive (hosted) videos
                elseif ($videoType === 'hosted' && $request->hasFile('video_file')) {
                    $videoFile = $request->file('video_file');
                    
                    // Validasi file
                    if (!$videoFile->isValid()) {
                        throw new \Exception('File video tidak valid atau corrupt');
                    }
                    
                    Log::info('Uploading to Google Drive', [
                        'original_name' => $videoFile->getClientOriginalName(),
                        'size' => $videoFile->getSize(),
                        'mime_type' => $videoFile->getMimeType()
                    ]);
                    
                    // Upload ke Google Drive
                    $driveInfo = $this->uploadToGoogleDrive($videoFile);
                    
                    if (!$driveInfo || !is_array($driveInfo)) {
                        throw new \Exception('Gagal upload video ke Google Drive. Response tidak valid.');
                    }
                    
                    // Validasi response dari Google Drive
                    if (empty($driveInfo['file_id'])) {
                        Log::error('Google Drive response missing file_id', $driveInfo);
                        throw new \Exception('Response dari Google Drive tidak lengkap.');
                    }
                    
                    // **PERBAIKAN: Buat struktur data yang konsisten dan valid JSON**
                    $videoData = [
                        'type' => 'hosted',
                        'file_id' => $driveInfo['file_id'] ?? null,
                        'file_name' => $driveInfo['file_name'] ?? $videoFile->getClientOriginalName(),
                        'original_name' => $driveInfo['original_name'] ?? $videoFile->getClientOriginalName(),
                        'web_view_link' => $driveInfo['web_view_link'] ?? null,
                        'web_content_link' => $driveInfo['web_content_link'] ?? null,
                        'embed_link' => $driveInfo['embed_link'] ?? 'https://drive.google.com/file/d/' . $driveInfo['file_id'] . '/preview',
                        'thumbnail_link' => $driveInfo['thumbnail_link'] ?? null,
                        'size' => $driveInfo['size'] ?? $videoFile->getSize(),
                        'mime_type' => $driveInfo['mime_type'] ?? $videoFile->getMimeType(),
                        'uploaded_at' => now()->toDateTimeString(),
                        'direct_play' => true,
                    ];
                    
                    // Hapus null values
                    $videoData = array_filter($videoData, function($value) {
                        return $value !== null;
                    });
                    
                    // Validasi JSON sebelum disimpan
                    $jsonTest = json_encode($videoData);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('JSON encode error for video_data', [
                            'error' => json_last_error_msg(),
                            'data' => $videoData
                        ]);
                        throw new \Exception('Gagal membuat data JSON video: ' . json_last_error_msg());
                    }
                    
                    Log::info('Google Drive upload successful', [
                        'file_id' => $driveInfo['file_id'],
                        'embed_link' => $videoData['embed_link']
                    ]);
                }
                
                // Local videos
                elseif ($videoType === 'local' && $request->hasFile('video_file')) {
                    $videoFile = $request->file('video_file');
                    
                    // Validasi file
                    if (!$videoFile->isValid()) {
                        throw new \Exception('File video tidak valid atau corrupt');
                    }
                    
                    Log::info('Uploading local video', [
                        'original_name' => $videoFile->getClientOriginalName(),
                        'size' => $videoFile->getSize(),
                        'mime_type' => $videoFile->getMimeType()
                    ]);
                    
                    // Simpan ke storage lokal
                    $videoFilePath = $videoFile->store('videos', 'public');
                    
                    if (!$videoFilePath) {
                        throw new \Exception('Gagal menyimpan video ke storage lokal');
                    }
                    
                    // Dapatkan durasi video
                    $videoDuration = $this->getVideoDuration($videoFile);
                    
                    // **PERBAIKAN: Buat struktur data yang konsisten dan valid JSON**
                    $videoData = [
                        'type' => 'local',
                        'path' => $videoFilePath,
                        'url' => Storage::url($videoFilePath),
                        'size' => Storage::disk('public')->size($videoFilePath),
                        'duration' => $videoDuration,
                        'original_name' => $videoFile->getClientOriginalName(),
                        'uploaded_at' => now()->toDateTimeString(),
                        'direct_play' => true,
                    ];
                    
                    // Validasi JSON sebelum disimpan
                    $jsonTest = json_encode($videoData);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // Hapus file jika JSON invalid
                        Storage::disk('public')->delete($videoFilePath);
                        Log::error('JSON encode error for local video', [
                            'error' => json_last_error_msg(),
                            'data' => $videoData
                        ]);
                        throw new \Exception('Gagal membuat data JSON video lokal: ' . json_last_error_msg());
                    }
                    
                    Log::info('Local video upload successful', [
                        'path' => $videoFilePath,
                        'url' => $videoData['url'],
                        'duration' => $videoDuration
                    ]);
                }
                
                // Video type tidak valid
                else {
                    throw new \Exception('Tipe video tidak didukung atau file tidak diupload');
                }
                
            } catch (\Exception $e) {
                Log::error('Error processing video upload: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                // Hapus file yang sudah diupload jika ada error
                if (isset($videoFilePath) && $videoFilePath) {
                    try {
                        Storage::disk('public')->delete($videoFilePath);
                    } catch (\Exception $deleteError) {
                        Log::error('Error deleting video file: ' . $deleteError->getMessage());
                    }
                }
                
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error processing video: ' . $e->getMessage()
                    ], 500);
                }
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error processing video: ' . $e->getMessage());
            }
        }

        // Hitung total durasi
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

        // Format soal pretest dengan VALIDASI JSON
        $soalPretest = null;
        if (in_array('pretest', $contentTypes) && !empty($request->pretest_soal)) {
            $soalFormatted = [];
            foreach ($request->pretest_soal as $index => $soal) {
                // Validasi bahwa semua pilihan terisi
                if (count(array_filter($soal['pilihan'])) < 4) {
                    $errorMsg = 'Semua pilihan jawaban untuk setiap soal harus diisi.';
                    
                    if ($isAjax) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMsg
                        ], 422);
                    }
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMsg);
                }

                $soalFormatted[] = [
                    'id' => $index + 1,
                    'pertanyaan' => $soal['pertanyaan'] ?? '',
                    'pilihan' => $soal['pilihan'] ?? [],
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0),
                    'poin' => (int)($soal['poin'] ?? 1)
                ];
            }
            
            // Validasi JSON sebelum disimpan
            $jsonTest = json_encode($soalFormatted);
            if (json_last_error() !== JSON_ERROR_NONE) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format soal pretest tidak valid: ' . json_last_error_msg()
                    ], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Format soal pretest tidak valid: ' . json_last_error_msg());
            }
            
            $soalPretest = $soalFormatted;
        }

        // Format soal posttest dengan VALIDASI JSON
        $soalPosttest = null;
        if (in_array('posttest', $contentTypes) && !empty($request->posttest_soal)) {
            $soalFormatted = [];
            foreach ($request->posttest_soal as $index => $soal) {
                // Validasi bahwa semua pilihan terisi
                if (count(array_filter($soal['pilihan'])) < 4) {
                    $errorMsg = 'Semua pilihan jawaban untuk setiap soal harus diisi.';
                    
                    if ($isAjax) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMsg
                        ], 422);
                    }
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMsg);
                }

                $soalFormatted[] = [
                    'id' => $index + 1,
                    'pertanyaan' => $soal['pertanyaan'] ?? '',
                    'pilihan' => $soal['pilihan'] ?? [],
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0),
                ];
            }
            
            // Validasi JSON sebelum disimpan
            $jsonTest = json_encode($soalFormatted);
            if (json_last_error() !== JSON_ERROR_NONE) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format soal posttest tidak valid: ' . json_last_error_msg()
                    ], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Format soal posttest tidak valid: ' . json_last_error_msg());
            }
            
            $soalPosttest = $soalFormatted;
        }

        // ============================================
        // PERBAIKAN: PLAYER CONFIG DENGAN VALIDASI JSON
        // ============================================
        
        $playerConfig = [
            'allow_skip' => $request->boolean('allow_skip'),
            'disable_forward_seek' => $request->boolean('disable_forward_seek', true),
            'disable_backward_seek' => $request->boolean('disable_backward_seek', false),
            'disable_right_click' => $request->boolean('disable_right_click', true),
            'require_completion' => $request->boolean('require_completion', true),
            'auto_pause_on_question' => $request->boolean('auto_pause_on_question', true),
            'require_question_completion' => $request->boolean('require_question_completion', false),
            'auto_detect_duration' => true,
            'player_type' => 'videojs',
            'videojs_options' => [
                'controls' => true,
                'autoplay' => false,
                'preload' => 'auto',
                'fluid' => true,
                'playbackRates' => [0.5, 1, 1.5, 2],
                'controlBar' => [
                    'volumePanel' => true,
                    'currentTimeDisplay' => true,
                    'timeDivider' => true,
                    'durationDisplay' => true,
                    'progressControl' => true,
                    'remainingTimeDisplay' => true,
                    'playbackRateMenuButton' => true,
                    'fullscreenToggle' => true,
                ],
                'html5' => [
                    'nativeTextTracks' => false
                ]
            ]
        ];

        // Validasi JSON untuk player_config
        $playerConfigJson = json_encode($playerConfig);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid player_config JSON: ' . json_last_error_msg());
            $playerConfig = []; // Fallback ke array kosong
        }

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

        // ============================================
        // PERBAIKAN: PERSIAPAN DATA UNTUK DISIMPAN
        // ============================================
        
        $materialData = [
            'course_id' => $kursus->id,
            'title' => $request->title,
            'description' => $request->description ?? '',
            'order' => $request->order,
            'type' => $type,
            'material_type' => $materialType,
            'duration' => $duration,
            'video_url' => $request->video_url ?? '',
            'video_type' => $request->video_type ?? 'youtube',
            'video_file' => $videoData, // Biarkan model handle JSON encoding
            'allow_skip' => $request->boolean('allow_skip'),
            'player_config' => $playerConfig, // Biarkan model handle JSON encoding
            'has_video_questions' => $hasVideoQuestions,
            'require_video_completion' => $request->boolean('require_completion', true),
            'question_count' => $questionCount,
            'total_video_points' => $totalVideoPoints,
            'is_active' => $request->boolean('is_active'),
            'attendance_required' => $request->boolean('attendance_required'),
            'learning_objectives' => json_encode($contentTypes),
            'auto_duration' => true,
        ];

        // Handle file_path dengan validasi JSON
        if (!empty($filePaths)) {
            $filePathsJson = json_encode($filePaths);
            if (json_last_error() === JSON_ERROR_NONE) {
                $materialData['file_path'] = $filePathsJson;
            } else {
                Log::error('Invalid JSON for file_paths: ' . json_last_error_msg());
                $materialData['file_path'] = null;
            }
        }

        // Handle soal pretest dengan validasi JSON
        if ($soalPretest) {
            $soalPretestJson = json_encode($soalPretest, JSON_UNESCAPED_UNICODE);
            if (json_last_error() === JSON_ERROR_NONE) {
                $materialData['soal_pretest'] = $soalPretestJson;
                $materialData['durasi_pretest'] = $request->durasi_pretest;
            } else {
                Log::error('Invalid JSON for soal_pretest: ' . json_last_error_msg());
            }
        }

        // Handle soal posttest dengan validasi JSON
        if ($soalPosttest) {
            $soalPosttestJson = json_encode($soalPosttest, JSON_UNESCAPED_UNICODE);
            if (json_last_error() === JSON_ERROR_NONE) {
                $materialData['soal_posttest'] = $soalPosttestJson;
                $materialData['durasi_posttest'] = $request->durasi_posttest;
            } else {
                Log::error('Invalid JSON for soal_posttest: ' . json_last_error_msg());
            }
        }

        // Log data sebelum disimpan untuk debugging
        Log::info('Material data prepared for save', [
            'has_video_data' => !empty($videoData),
            'video_data_type' => gettype($videoData),
            'video_data_sample' => is_array($videoData) ? array_keys($videoData) : 'Not array',
            'has_player_config' => !empty($playerConfig),
            'content_types' => $contentTypes
        ]);

        try {
            // Create material - Model akan handle JSON encoding via mutator
            $material = Materials::create($materialData);
            
            Log::info('Material created successfully', [
                'material_id' => $material->id,
                'video_type' => $material->video_type,
                'video_file_exists' => !empty($material->video_file),
                'video_file_type' => gettype($material->video_file),
                'is_video_available' => $material->isVideoAvailable()
            ]);
            
            // Save video questions jika ada
            if ($hasVideoQuestions && $request->filled('video_questions')) {
                foreach ($request->video_questions as $index => $questionData) {
                    if (empty($questionData['question']) || empty($questionData['options'])) {
                        continue;
                    }
                    
                    // Validasi JSON untuk options
                    $optionsJson = json_encode($questionData['options']);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('Invalid JSON for video question options', [
                            'material_id' => $material->id,
                            'question_index' => $index
                        ]);
                        continue;
                    }
                    
                    VideoQuestion::create([
                        'material_id' => $material->id,
                        'order' => $index + 1,
                        'time_in_seconds' => $questionData['time_in_seconds'] ?? 0,
                        'question' => $questionData['question'],
                        'options' => $optionsJson,
                        'correct_option' => $questionData['correct_option'] ?? 0,
                        'points' => $questionData['points'] ?? 1,
                        'explanation' => $questionData['explanation'] ?? null,
                        'required_to_continue' => $questionData['required_to_continue'] ?? true,
                    ]);
                }
            }
            
            // **FIX: SELALU KEMBALIKAN JSON UNTUK AJAX REQUEST**
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Material berhasil ditambahkan!',
                    'redirect' => route('admin.kursus.materials.index', $kursus),
                    'material_id' => $material->id,
                    'video_status' => [
                        'is_available' => $material->isVideoAvailable(),
                        'type' => $material->video_type,
                        'has_video_data' => !empty($material->video_file)
                    ]
                ]);
            }
            
            // Hanya untuk non-AJAX request
            return redirect()->route('admin.kursus.materials.index', $kursus)
                            ->with('success', 'Material berhasil ditambahkan!');
                            
        } catch (\Exception $e) {
            Log::error('Error creating material record: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            // Hapus file yang sudah diupload jika ada error
            foreach ($filePaths as $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            
            // Hapus video file dari Google Drive jika ada error
            if (isset($driveInfo) && isset($driveInfo['file_id'])) {
                try {
                    $this->deleteFromGoogleDrive($driveInfo['file_id']);
                } catch (\Exception $deleteError) {
                    Log::error('Error deleting video from Google Drive: ' . $deleteError->getMessage());
                }
            }
            
            // Hapus video local jika ada error
            if ($videoFilePath) {
                try {
                    Storage::disk('public')->delete($videoFilePath);
                } catch (\Exception $deleteError) {
                    Log::error('Error deleting local video: ' . $deleteError->getMessage());
                }
            }
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan material: ' . $e->getMessage(),
                    'error_details' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menyimpan material: ' . $e->getMessage());
        }
                        
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation Exception:', $e->errors());
        
        if ($isAjax) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
        
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
            
    } catch (\Exception $e) {
        Log::error('Error storing material: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        
        // Hapus file yang sudah diupload jika ada error
        foreach ($filePaths as $filePath) {
            Storage::disk('public')->delete($filePath);
        }
        
        // Hapus video file dari Google Drive jika ada error
        if (isset($driveInfo) && isset($driveInfo['file_id'])) {
            try {
                $this->deleteFromGoogleDrive($driveInfo['file_id']);
            } catch (\Exception $deleteError) {
                Log::error('Error deleting video from Google Drive: ' . $deleteError->getMessage());
            }
        }
        
        // Hapus video local jika ada error
        if (isset($videoFilePath) && $videoFilePath) {
            try {
                Storage::disk('public')->delete($videoFilePath);
            } catch (\Exception $deleteError) {
                Log::error('Error deleting local video: ' . $deleteError->getMessage());
            }
        }
        
        if ($isAjax) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan material: ' . $e->getMessage(),
                'error_details' => $e->getMessage()
            ], 500);
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
            
            // Video specific validations - PERBAIKAN: HANYA youtube, hosted, local
            'video_type' => 'nullable|required_if:content_types,video|in:youtube,hosted,local',
            'video_url' => 'nullable|required_if:video_type,youtube|url',
            'video_file' => 'nullable|file|mimes:mp4,webm,avi,mov,wmv,mkv|max:102400',
            'allow_skip' => 'boolean',
            
            // Player config validations
            'disable_forward_seek' => 'boolean',
            'disable_backward_seek' => 'boolean',
            'disable_right_click' => 'boolean',
            'require_completion' => 'boolean',
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

        // Handle video upload
        $videoInfo = $material->video_file;
        $videoDuration = $material->duration;
        
        if (in_array('video', $contentTypes)) {
            // Jika video_type = hosted dan ada file baru
            if ($request->video_type === 'hosted' && $request->hasFile('video_file')) {
                try {
                    // Delete old video file jika ada
                    if ($material->video_file) {
                        $oldVideoData = json_decode($material->video_file, true);
                        if ($oldVideoData && isset($oldVideoData['type']) && $oldVideoData['type'] === 'hosted') {
                            if (isset($oldVideoData['file_id'])) {
                                $this->deleteFromGoogleDrive($oldVideoData['file_id']);
                            }
                        } elseif ($oldVideoData && isset($oldVideoData['type']) && $oldVideoData['type'] === 'local') {
                            // Hapus file lokal
                            if (isset($oldVideoData['path'])) {
                                Storage::disk('public')->delete($oldVideoData['path']);
                            }
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
            
            // Jika video_type = local dan ada file baru
            elseif ($request->video_type === 'local' && $request->hasFile('video_file')) {
                try {
                    // Delete old video file jika ada
                    if ($material->video_file) {
                        $oldVideoData = json_decode($material->video_file, true);
                        if ($oldVideoData && isset($oldVideoData['type']) && $oldVideoData['type'] === 'local') {
                            // Hapus file lokal
                            if (isset($oldVideoData['path'])) {
                                Storage::disk('public')->delete($oldVideoData['path']);
                            }
                        }
                    }
                    
                    $videoFile = $request->file('video_file');
                    $videoPath = $videoFile->store('videos', 'public');
                    $videoDuration = $this->getVideoDuration($videoFile);
                    
                    $videoInfo = json_encode([
                        'type' => 'local',
                        'path' => $videoPath,
                        'url' => Storage::url($videoPath),
                        'size' => Storage::disk('public')->size($videoPath),
                        'duration' => $videoDuration,
                        'uploaded_at' => now()->toDateTimeString(),
                        'direct_play' => true,
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Error uploading video to local storage: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal mengupload video: ' . $e->getMessage());
                }
            }
            
            // Jika video_type = youtube
            elseif ($request->video_type === 'youtube') {
                if (!$this->isValidYouTubeUrl($request->video_url)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'URL YouTube tidak valid.');
                }
                
                // Reset video_info untuk YouTube
                $videoInfo = null;
                $videoDuration = 0;
            }
        } // PERBAIKAN: Tutup if statement ini dengan benar

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
            'auto_pause_on_question' => $request->boolean('auto_pause_on_question', true),
            'require_question_completion' => $request->boolean('require_question_completion', false),
            'auto_detect_duration' => true,
            'player_type' => 'videojs',
            'videojs_options' => [
                'controls' => true,
                'autoplay' => false,
                'preload' => 'auto',
                'fluid' => true,
                'playbackRates' => [0.5, 1, 1.5, 2],
                'controlBar' => [
                    'volumePanel' => true,
                    'currentTimeDisplay' => true,
                    'timeDivider' => true,
                    'durationDisplay' => true,
                    'progressControl' => true,
                    'remainingTimeDisplay' => true,
                    'playbackRateMenuButton' => true,
                    'fullscreenToggle' => true,
                ],
                'html5' => [
                    'nativeTextTracks' => false
                ]
            ]
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
            $materialData['video_type'] = 'youtube'; // PERBAIKAN: ganti dari 'external' ke 'youtube'
            $materialData['video_file'] = null;
            $materialData['player_config'] = null;
            $materialData['has_video_questions'] = false;
            $materialData['question_count'] = 0;
            $materialData['total_video_points'] = 0;
            
            // Hapus file video jika ada
            if ($material->video_file) {
                try {
                    $oldVideoData = json_decode($material->video_file, true);
                    if ($oldVideoData && isset($oldVideoData['type'])) {
                        if ($oldVideoData['type'] === 'hosted' && isset($oldVideoData['file_id'])) {
                            $this->deleteFromGoogleDrive($oldVideoData['file_id']);
                        } elseif ($oldVideoData['type'] === 'local' && isset($oldVideoData['path'])) {
                            Storage::disk('public')->delete($oldVideoData['path']);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting video file: ' . $e->getMessage());
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
            
            // Hapus video file baru dari local storage jika ada error
            if ($request->hasFile('video_file') && isset($videoPath)) {
                try {
                    Storage::disk('public')->delete($videoPath);
                } catch (\Exception $deleteError) {
                    Log::error('Error deleting new video from local storage: ' . $deleteError->getMessage());
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
            // PERBAIKAN: Cek apakah file_path sudah array atau masih string JSON
            $files = [];
            
            if (is_array($material->file_path)) {
                // Jika sudah array, langsung gunakan
                $files = $material->file_path;
            } elseif (is_string($material->file_path) && !empty($material->file_path)) {
                // Jika string, decode JSON
                $decoded = json_decode($material->file_path, true);
                if (is_array($decoded)) {
                    $files = $decoded;
                }
            }
            
            // Hapus file dari storage
            if (is_array($files)) {
                foreach ($files as $filePath) {
                    if (is_string($filePath) && !empty($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }
        }

        // Delete video file dari Google Drive
        if ($material->video_file) {
            try {
                $videoData = [];
                
                // PERBAIKAN: Handle video_file yang bisa array atau string JSON
                if (is_array($material->video_file)) {
                    $videoData = $material->video_file;
                } elseif (is_string($material->video_file) && !empty($material->video_file)) {
                    $decoded = json_decode($material->video_file, true);
                    if (is_array($decoded)) {
                        $videoData = $decoded;
                    }
                }
                
                if (!empty($videoData)) {
                    if (isset($videoData['type']) && $videoData['type'] === 'hosted' && isset($videoData['file_id'])) {
                        // Hapus dari Google Drive
                        $this->deleteFromGoogleDrive($videoData['file_id']);
                    } elseif (isset($videoData['type']) && $videoData['type'] === 'local' && isset($videoData['path'])) {
                        // Hapus dari local storage
                        Storage::disk('public')->delete($videoData['path']);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error deleting video file: ' . $e->getMessage());
            }
        }

        // Delete video questions
        VideoQuestion::where('material_id', $material->id)->delete();
        
        // Delete video progress
        UserVideoProgress::where('material_id', $material->id)->delete();

        // Delete material progress
        MaterialProgress::where('material_id', $material->id)->delete();

        // Delete material
        $material->delete();

        // Reorder secara manual (backup jika event deleted tidak jalan)
        $this->reorderMaterials($kursus->id);

        return redirect()->route('admin.kursus.materials.index', $kursus)
                        ->with('success', 'Material berhasil dihapus!');
    } catch (\Exception $e) {
        Log::error('Error deleting material: ' . $e->getMessage());
        Log::error('File path data: ' . print_r($material->file_path, true));
        Log::error('Video file data: ' . print_r($material->video_file, true));
        
        return redirect()->back()
                        ->with('error', 'Gagal menghapus material: ' . $e->getMessage());
    }
}

private function isValidYouTubeUrl($url)
    {
        if (empty($url)) {
            return false;
        }
        
        $patterns = [
            '/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/',
            '/^(https?\:\/\/)?(www\.)?youtube\.com\/watch\?v=[\w-]+/',
            '/^(https?\:\/\/)?(www\.)?youtu\.be\/[\w-]+/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        
        return false;
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
            throw new \Exception('Credentials file not found at: ' . $credentialsPath);
        }
        
        $client->setAuthConfig($credentialsPath);
        $client->addScope(GoogleDrive::DRIVE_FILE);
        $client->addScope(GoogleDrive::DRIVE_READONLY);
        
        // Set subject untuk service account
        $serviceAccount = json_decode(file_get_contents($credentialsPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid credentials JSON: ' . json_last_error_msg());
        }
        
        if (!isset($serviceAccount['client_email'])) {
            throw new \Exception('Service account email not found in credentials');
        }
        
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
            
            Log::info('Google Drive permission set successfully', [
                'file_id' => $uploadedFile->id
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to make file public, but continuing: ' . $e->getMessage());
        }
        
        // Buat embed link khusus
        $embedLink = 'https://drive.google.com/file/d/' . $uploadedFile->id . '/preview';
        
        // **PERBAIKAN: Return data yang konsisten dan lengkap**
        $result = [
            'file_id' => $uploadedFile->id,
            'file_name' => $uploadedFile->name,
            'original_name' => $originalName,
            'web_view_link' => $uploadedFile->webViewLink ?? null,
            'web_content_link' => $uploadedFile->webContentLink ?? null,
            'embed_link' => $embedLink,
            'thumbnail_link' => $uploadedFile->thumbnailLink ?? null,
            'size' => $uploadedFile->size ?? null,
            'mime_type' => $uploadedFile->mimeType ?? null,
            'uploaded_at' => now()->toDateTimeString(),
            'direct_play' => true,
        ];
        
        // Hapus null values
        $result = array_filter($result, function($value) {
            return $value !== null;
        });
        
        // Validasi JSON
        $jsonTest = json_encode($result);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON from Google Drive upload', [
                'error' => json_last_error_msg(),
                'data' => $result
            ]);
            throw new \Exception('Invalid JSON data from Google Drive: ' . json_last_error_msg());
        }
        
        Log::info('Google Drive upload successful', [
            'file_id' => $uploadedFile->id,
            'embed_link' => $embedLink,
            'json_valid' => true
        ]);
        
        return $result;
        
    } catch (\Exception $e) {
        Log::error('Error in uploadToGoogleDrive: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        throw new \Exception('Google Drive upload failed: ' . $e->getMessage());
    }
}

    /**
 * Fix video data for all materials
 */
public function fixAllVideoData()
{
    try {
        $materials = Materials::whereIn('video_type', ['hosted', 'local'])
            ->get();
        
        $fixed = 0;
        $errors = [];
        
        foreach ($materials as $material) {
            try {
                $videoFile = $material->video_file;
                
                // Jika video_file adalah string tapi bukan JSON valid
                if (is_string($videoFile) && !empty($videoFile)) {
                    // Coba decode
                    $decoded = json_decode($videoFile, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning('Fixing invalid JSON for material ' . $material->id, [
                            'error' => json_last_error_msg(),
                            'video_file_sample' => substr($videoFile, 0, 200)
                        ]);
                        
                        // Set ke null
                        $material->video_file = null;
                        $material->save();
                        $fixed++;
                    } elseif (is_array($decoded)) {
                        // JSON valid, simpan ulang untuk memastikan
                        $material->video_file = $decoded;
                        $material->save();
                        $fixed++;
                    }
                }
                // Jika video_file sudah array, simpan ulang untuk validasi
                elseif (is_array($videoFile)) {
                    $material->video_file = $videoFile;
                    $material->save();
                    $fixed++;
                }
                
            } catch (\Exception $e) {
                $errors[] = 'Material ' . $material->id . ': ' . $e->getMessage();
                Log::error('Error fixing video_file for material ' . $material->id . ': ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Fixed ' . $fixed . ' materials',
            'errors' => $errors
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in fixAllVideoData: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Check video data status
 */
public function checkVideoDataStatus()
{
    try {
        $materials = Materials::all();
        
        $results = [];
        $invalidCount = 0;
        $validCount = 0;
        
        foreach ($materials as $material) {
            $videoFile = $material->video_file;
            $isValid = true;
            $error = null;
            
            // Check if video_file is valid
            if (is_string($videoFile) && !empty($videoFile)) {
                json_decode($videoFile);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $isValid = false;
                    $error = 'Invalid JSON: ' . json_last_error_msg();
                    $invalidCount++;
                } else {
                    $validCount++;
                }
            } elseif (is_array($videoFile)) {
                $validCount++;
            } elseif (empty($videoFile) || $videoFile === null) {
                // Empty is okay for non-video materials
                $validCount++;
            }
            
            $results[] = [
                'id' => $material->id,
                'title' => $material->title,
                'video_type' => $material->video_type,
                'video_file_type' => gettype($videoFile),
                'is_valid' => $isValid,
                'error' => $error,
                'is_video_available' => $material->isVideoAvailable()
            ];
        }
        
        return response()->json([
            'success' => true,
            'total_materials' => count($materials),
            'valid_video_data' => $validCount,
            'invalid_video_data' => $invalidCount,
            'results' => $results
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error checking video data: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
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
    if ($material->video_type === 'local' && $material->video_file) {
        try {
            $videoData = json_decode($material->video_file, true);
            if ($videoData && isset($videoData['url'])) {
                return $videoData['url'];
            }
        } catch (\Exception $e) {
            Log::error('Error getting local video URL: ' . $e->getMessage());
        }
    }
    
    if ($material->video_type === 'hosted' && $material->video_file) {
        try {
            $videoData = json_decode($material->video_file, true);
            if ($videoData && isset($videoData['embed_link'])) {
                return $videoData['embed_link'];
            }
        } catch (\Exception $e) {
            Log::error('Error getting hosted video URL: ' . $e->getMessage());
        }
    }
    
    // YouTube
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
        
        // Untuk YouTube, extract ID dari URL
        if ($material->video_type === 'youtube') {
            return $this->extractYouTubeId($material->video_url);
        }
        
        return null;
    }

    /**
     * Get video embed URL
     */
    private function getVideoEmbedUrl($material)
{
    if ($material->video_type === 'youtube') {
        $videoId = $this->extractYouTubeId($material->video_url);
        if ($videoId) {
            return 'https://www.youtube.com/embed/' . $videoId . '?rel=0&modestbranding=1&showinfo=0';
        }
    }
    elseif ($material->video_type === 'hosted' && $material->video_file) {
        try {
            $videoData = json_decode($material->video_file, true);
            if ($videoData && isset($videoData['embed_link'])) {
                return $videoData['embed_link'];
            }
        } catch (\Exception $e) {
            Log::error('Error getting hosted embed URL: ' . $e->getMessage());
        }
    }
    
    return null;
}

    private function extractYouTubeId($url)
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
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
            Log::error('Error importing soal: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function prepareVideoData($material)
{
    $videoData = [
        'type' => $material->video_type ?? 'youtube',
        'url' => $material->video_url ?? '',
        'embed_url' => null,
        'direct_link' => null,
        'is_local' => false,
        'is_hosted' => false,
        'video_info' => null,
        'player_config' => $material->player_config ? json_decode($material->player_config, true) : [],
        'duration' => $material->duration,
        'auto_play' => false,
        'controls' => true,
        'player_type' => 'videojs'
    ];
    
    // YouTube
    if ($material->video_type === 'youtube' && $material->video_url) {
        $videoId = $this->extractYouTubeId($material->video_url);
        if ($videoId) {
            $videoData['embed_url'] = "https://www.youtube.com/embed/{$videoId}?rel=0&modestbranding=1&showinfo=0";
            $videoData['direct_link'] = "https://www.youtube.com/watch?v={$videoId}";
        }
    }
    
    // Google Drive (hosted)
    elseif ($material->video_type === 'hosted' && $material->video_file) {
        try {
            $videoInfo = json_decode($material->video_file, true);
            if ($videoInfo) {
                $videoData['is_hosted'] = true;
                $videoData['video_info'] = $videoInfo;
                
                if (isset($videoInfo['embed_link'])) {
                    $videoData['embed_url'] = $videoInfo['embed_link'];
                    $videoData['direct_link'] = $videoInfo['web_view_link'] ?? $videoInfo['embed_link'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error preparing hosted video data: ' . $e->getMessage());
        }
    }
    
    // Local video
    elseif ($material->video_type === 'local' && $material->video_file) {
        try {
            $videoInfo = json_decode($material->video_file, true);
            if ($videoInfo) {
                $videoData['is_local'] = true;
                $videoData['video_info'] = $videoInfo;
                $videoData['direct_link'] = $videoInfo['url'] ?? Storage::url($videoInfo['path'] ?? '');
                $videoData['player_type'] = 'videojs';
                
                if (isset($videoInfo['size']) && $videoInfo['size'] > 100 * 1024 * 1024) {
                    $videoData['use_hls'] = true;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error preparing local video data: ' . $e->getMessage());
        }
    }
    
    return $videoData;
}

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
            Log::warning("Soal pada baris {$soal['row_number']} diabaikan: kurang dari 2 pilihan yang valid");
            continue;
        }
        
        $soalData[] = $soal;
        
        // Batasi maksimal 500 soal untuk performance
        if (count($soalData) >= 500) {
            Log::info("Mencapai batas maksimal 500 soal, menghentikan proses import");
            break;
        }
    }
    
    Log::info("Berhasil memproses " . count($soalData) . " soal dari Excel");
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
    try {
        // Cek apakah FFmpeg tersedia di Windows Laragon
        $ffmpegPath = 'C:\laragon\bin\ffmpeg\bin\ffmpeg.exe';
        
        // Jika tidak ada di path default, coba cari di PATH
        if (!file_exists($ffmpegPath)) {
            $ffmpegPath = 'ffmpeg';
        }
        
        // Cek jika FFmpeg tersedia
        $tempPath = $file->getRealPath();
        
        // Gunakan shell_exec untuk Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Command untuk Windows
            $command = "\"$ffmpegPath\" -i \"" . escapeshellarg($tempPath) . "\" 2>&1";
        } else {
            // Command untuk Linux/Mac
            $command = "$ffmpegPath -i " . escapeshellarg($tempPath) . " 2>&1";
        }
        
        $output = shell_exec($command);
        
        if ($output) {
            // Parse duration dari output
            preg_match('/Duration: (\d{2}):(\d{2}):(\d{2})\.\d{2}/', $output, $matches);
            
            if (count($matches) >= 4) {
                $hours = (int)$matches[1];
                $minutes = (int)$matches[2];
                $seconds = (int)$matches[3];
                
                $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
                return (int)$totalSeconds;
            }
        }
        
        // Fallback: Coba dengan alternatif (getID3)
        if (class_exists('\getID3')) {
            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($tempPath);
            
            if (isset($fileInfo['playtime_seconds'])) {
                return (int)$fileInfo['playtime_seconds'];
            }
        }
        
        // Fallback: Coba menggunakan PHP-FFMpeg jika tersedia
        if (class_exists('\FFMpeg\FFMpeg')) {
            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($tempPath);
            $duration = $video->getFormat()->get('duration');
            
            return (int)$duration;
        }
        
    } catch (\Exception $e) {
        Log::warning('Error getting video duration: ' . $e->getMessage());
    }
    
    // Default: 0 (durasi tidak diketahui)
    return 0;
}
}