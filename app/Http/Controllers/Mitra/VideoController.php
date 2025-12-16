<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Materials;
use App\Models\VideoQuestion;
use App\Models\UserVideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function show(Kursus $kursus, Materials $material)
    {
        // Check if material belongs to course
        if ($material->course_id !== $kursus->id) {
            abort(404);
        }

        // Check if material has video
        $contentTypes = json_decode($material->learning_objectives, true) ?? [];
        if (!in_array('video', $contentTypes)) {
            abort(404);
        }

        // Get or create user progress
        $progress = UserVideoProgress::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->first();
        
        if (!$progress) {
            $progress = UserVideoProgress::create([
                'user_id' => Auth::id(),
                'material_id' => $material->id,
                'progress_percentage' => 0,
                'last_watched_second' => 0,
                'is_completed' => false,
                'total_points_earned' => 0,
                'watch_history' => [],
                'answered_questions' => []
            ]);
        }

        // Get video questions
        $videoQuestions = VideoQuestion::where('material_id', $material->id)
            ->orderBy('time_in_seconds')
            ->get();

        // Get answered questions from progress
        $answeredQuestions = $progress->answered_questions ?? [];

        // Get watch history from progress
        $watchHistory = $progress->watch_history ?? [];

        // Determine video type and URL - PERBAIKAN DI SINI
        $videoType = $material->video_type ?? 'external';
        $videoUrl = '';
        $videoId = null;

        switch ($videoType) {
            case 'youtube':
                $videoUrl = $material->video_url ?? '';
                if ($videoUrl) {
                    // Ekstrak ID YouTube dengan regex yang lebih baik
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                    $videoId = $matches[1] ?? null;
                    
                    // Jika tidak bisa diekstrak, coba ambil langsung dari video_url jika sudah ID
                    if (!$videoId && strlen($videoUrl) === 11) {
                        $videoId = $videoUrl;
                    }
                }
                break;
                
            case 'vimeo':
                $videoUrl = $material->video_url ?? '';
                if ($videoUrl) {
                    preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $videoUrl, $matches);
                    $videoId = $matches[1] ?? null;
                }
                break;
                
            case 'hosted':
                // Untuk video hosted, gunakan video_file
                if ($material->video_file) {
                    $videoUrl = Storage::url($material->video_file);
                }
                break;
                
            case 'external':
            default:
                $videoUrl = $material->video_url ?? '';
                break;
        }

        // Debug: Tambahkan logging untuk memeriksa data video
        \Log::info('Video Debug:', [
            'material_id' => $material->id,
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'video_id' => $videoId,
            'video_file' => $material->video_file,
            'has_video' => !empty($videoUrl) || !empty($videoId)
        ]);

        // Get player config dengan cara yang lebih sederhana
        $defaultPlayerConfig = [
            'allow_skip' => false,
            'disable_forward_seek' => true,
            'disable_backward_seek' => false,
            'disable_right_click' => true,
            'require_completion' => true,
            'min_watch_percentage' => 90,
            'auto_pause_on_question' => true,
            'require_question_completion' => false,
        ];
        
        // Menggunakan getPlayerConfigAttribute dari model (asumsi ada accessor)
        $playerConfig = $defaultPlayerConfig;
        if ($material->player_config) {
            $config = is_array($material->player_config) 
                ? $material->player_config 
                : json_decode($material->player_config, true);
            
            if (is_array($config)) {
                $playerConfig = array_merge($defaultPlayerConfig, $config);
            }
        }

        // Get next material
        $nextMaterial = Materials::where('course_id', $kursus->id)
            ->where('order', '>', $material->order)
            ->where('is_active', true)
            ->orderBy('order')
            ->first();

        // Prepare data for view
        $viewData = [
            'kursus' => $kursus,
            'material' => $material,
            'progress' => $progress,
            'videoQuestions' => $videoQuestions,
            'answeredQuestions' => $answeredQuestions,
            'videoUrl' => $videoUrl,
            'videoType' => $videoType,
            'videoId' => $videoId,
            'playerConfig' => $playerConfig,
            'nextMaterial' => $nextMaterial,
            'watchHistory' => $watchHistory,
        ];

        return view('mitra.video-viewer', $viewData);
    }


    public function saveProgress(Request $request, Kursus $kursus, Materials $material)
    {
        $request->validate([
            'current_time' => 'required|numeric|min:0',
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'max_watched_time' => 'required|numeric|min:0',
            'watch_history' => 'nullable|array'
        ]);

        $progress = UserVideoProgress::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->first();

        if (!$progress) {
            $progress = UserVideoProgress::create([
                'user_id' => Auth::id(),
                'material_id' => $material->id,
                'progress_percentage' => 0,
                'last_watched_second' => 0,
                'is_completed' => false,
                'total_points_earned' => 0,
                'watch_history' => [],
                'answered_questions' => []
            ]);
        }

        // Check if we can update max_watched_time
        $playerConfig = $material->player_config ? json_decode($material->player_config, true) : [];
        if (empty($playerConfig) || !isset($playerConfig['disable_forward_seek']) || !$playerConfig['disable_forward_seek'] || 
            $request->max_watched_time <= $progress->last_watched_second) {
            $progress->last_watched_second = $request->current_time;
            $progress->max_watched_time = $request->max_watched_time;
        }

        $progress->progress_percentage = max($progress->progress_percentage, $request->progress_percentage);
        
        // Store watch history properly
        if ($request->watch_history && is_array($request->watch_history)) {
            $currentHistory = is_array($progress->watch_history) ? $progress->watch_history : [];
            $newHistory = array_merge($currentHistory, $request->watch_history);
            $progress->watch_history = array_unique($newHistory);
        }

        // Check if video is completed
        $minWatchPercentage = isset($playerConfig['min_watch_percentage']) ? $playerConfig['min_watch_percentage'] : 90;
        if ($request->progress_percentage >= 100 || 
            (isset($playerConfig['require_completion']) && $playerConfig['require_completion'] && 
             $request->progress_percentage >= $minWatchPercentage)) {
            
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        return response()->json([
            'success' => true,
            'progress' => $progress->progress_percentage
        ]);
    }

    public function answerQuestion(Request $request, Kursus $kursus, Materials $material)
    {
        $request->validate([
            'question_id' => 'required|exists:video_questions,id',
            'answer' => 'required|integer|min:0|max:3',
            'is_correct' => 'required|boolean'
        ]);

        $question = VideoQuestion::findOrFail($request->question_id);
        $progress = UserVideoProgress::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->first();

        if (!$progress) {
            $progress = UserVideoProgress::create([
                'user_id' => Auth::id(),
                'material_id' => $material->id,
                'progress_percentage' => 0,
                'last_watched_second' => 0,
                'is_completed' => false,
                'total_points_earned' => 0,
                'watch_history' => [],
                'answered_questions' => []
            ]);
        }

        // Get current answered questions
        $answeredQuestions = [];
        if ($progress->answered_questions) {
            if (is_string($progress->answered_questions)) {
                $answeredQuestions = json_decode($progress->answered_questions, true) ?? [];
            } elseif (is_array($progress->answered_questions)) {
                $answeredQuestions = $progress->answered_questions;
            }
        }
        
        $pointsEarned = 0;
        if ($request->is_correct) {
            $pointsEarned = $question->points;
            $progress->total_points_earned += $pointsEarned;
        }

        // Record answer
        $answeredQuestions[$question->id] = [
            'question_id' => $question->id,
            'answer' => $request->answer,
            'is_correct' => $request->is_correct,
            'points_earned' => $pointsEarned,
            'answered_at' => now()->toDateTimeString()
        ];

        $progress->answered_questions = $answeredQuestions;
        $progress->save();

        return response()->json([
            'success' => true,
            'points_earned' => $pointsEarned,
            'total_points' => $progress->total_points_earned
        ]);
    }

    public function markAsCompleted(Request $request, Kursus $kursus, Materials $material)
    {
        $progress = UserVideoProgress::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->first();

        if (!$progress) {
            $progress = UserVideoProgress::create([
                'user_id' => Auth::id(),
                'material_id' => $material->id,
                'progress_percentage' => 0,
                'last_watched_second' => 0,
                'is_completed' => false,
                'total_points_earned' => 0,
                'watch_history' => [],
                'answered_questions' => []
            ]);
        }

        // Get player config
        $playerConfig = $material->player_config ? json_decode($material->player_config, true) : [];
        $defaultPlayerConfig = [
            'require_completion' => true,
            'min_watch_percentage' => 90,
            'require_question_completion' => false,
        ];
        
        if (is_array($playerConfig)) {
            $playerConfig = array_merge($defaultPlayerConfig, $playerConfig);
        } else {
            $playerConfig = $defaultPlayerConfig;
        }

        // Check if all requirements are met
        $canComplete = true;
        $message = '';

        // Check watch percentage
        if ($playerConfig['require_completion'] && $progress->progress_percentage < $playerConfig['min_watch_percentage']) {
            $canComplete = false;
            $message = 'Anda harus menonton minimal ' . $playerConfig['min_watch_percentage'] . '% dari video';
        }

        // Check required questions
        if ($playerConfig['require_question_completion'] && $material->has_video_questions) {
            $requiredQuestions = VideoQuestion::where('material_id', $material->id)
                ->where('required_to_continue', true)
                ->get();
            
            $answeredQuestions = [];
            if ($progress->answered_questions) {
                if (is_string($progress->answered_questions)) {
                    $answeredQuestions = json_decode($progress->answered_questions, true) ?? [];
                } elseif (is_array($progress->answered_questions)) {
                    $answeredQuestions = $progress->answered_questions;
                }
            }
            
            foreach ($requiredQuestions as $question) {
                if (!isset($answeredQuestions[$question->id])) {
                    $canComplete = false;
                    $message = 'Anda harus menjawab semua pertanyaan wajib terlebih dahulu';
                    break;
                }
            }
        }

        if (!$canComplete) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        // Mark as completed
        $progress->is_completed = true;
        $progress->completed_at = now();
        $progress->progress_percentage = 100;
        $progress->save();

        return response()->json([
            'success' => true,
            'message' => 'Video berhasil ditandai sebagai telah ditonton',
            'progress' => $progress
        ]);
    }
}