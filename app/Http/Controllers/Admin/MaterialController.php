<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Materials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        // Validasi dasar
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'content_types' => 'required|array|min:1',
            'content_types.*' => 'in:file,video,pretest,posttest',
            'attendance_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Conditional validasi untuk file
        if (in_array('file', $request->content_types)) {
            $request->validate([
                'file_path' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
                'duration_file' => 'required|integer|min:1',
            ]);
        } else {
            // Jika file tidak dipilih, set default values
            $request->merge([
                'file_path' => null,
                'duration_file' => 0,
            ]);
        }

        // Conditional validasi untuk video
        if (in_array('video', $request->content_types)) {
            $request->validate([
                'video_url' => 'required|url',
                'duration_video' => 'required|integer|min:1',
            ]);
        } else {
            // Jika video tidak dipilih, set default values
            $request->merge([
                'video_url' => '',
                'duration_video' => 0,
            ]);
        }

        // Conditional validasi untuk pretest
        if (in_array('pretest', $request->content_types)) {
            $request->validate([
                'durasi_pretest' => 'required|integer|min:1',
                'passing_grade_pretest' => 'required|integer|min:1|max:100',
                'pretest_soal' => 'required|array|min:1',
                'pretest_soal.*.pertanyaan' => 'required|string',
                'pretest_soal.*.pilihan' => 'required|array|min:2',
                'pretest_soal.*.pilihan.*' => 'required|string',
                'pretest_soal.*.jawaban_benar' => 'required|integer|min:0',
            ]);
        } else {
            // Jika pretest tidak dipilih, set default values
            $request->merge([
                'durasi_pretest' => 0,
                'passing_grade_pretest' => 0,
                'pretest_soal' => [],
            ]);
        }

        // Conditional validasi untuk posttest
        if (in_array('posttest', $request->content_types)) {
            $request->validate([
                'durasi_posttest' => 'required|integer|min:1',
                'passing_grade_posttest' => 'required|integer|min:1|max:100',
                'posttest_soal' => 'required|array|min:1',
                'posttest_soal.*.pertanyaan' => 'required|string',
                'posttest_soal.*.pilihan' => 'required|array|min:2',
                'posttest_soal.*.pilihan.*' => 'required|string',
                'posttest_soal.*.jawaban_benar' => 'required|integer|min:0',
            ]);
        } else {
            // Jika posttest tidak dipilih, set default values
            $request->merge([
                'durasi_posttest' => 0,
                'passing_grade_posttest' => 0,
                'posttest_soal' => [],
            ]);
        }

        // Handle file upload
        $filePath = null;
        if (in_array('file', $request->content_types) && $request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('materials', 'public');
        }

        // Hitung total durasi dari file dan video
        $duration = 0;
        if (in_array('file', $request->content_types)) {
            $duration += $request->duration_file;
        }
        if (in_array('video', $request->content_types)) {
            $duration += $request->duration_video;
        }

        // Tentukan type berdasarkan content_types
        $type = 'material';
        if (in_array('pretest', $request->content_types)) {
            $type = 'pre_test';
        } elseif (in_array('posttest', $request->content_types)) {
            $type = 'post_test';
        }

        // Tentukan material_type berdasarkan content_types (gunakan nilai pertama)
        $materialType = 'theory'; // default
        if (in_array('file', $request->content_types)) {
            $materialType = 'theory';
        } elseif (in_array('video', $request->content_types)) {
            $materialType = 'video';
        } elseif (in_array('pretest', $request->content_types)) {
            $materialType = 'quiz';
        } elseif (in_array('posttest', $request->content_types)) {
            $materialType = 'quiz';
        }

        // Format soal pretest
        $soalPretest = null;
        if (in_array('pretest', $request->content_types) && !empty($request->pretest_soal)) {
            $soalFormatted = [];
            foreach ($request->pretest_soal as $index => $soal) {
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
                $soalFormatted[] = [
                    'id' => $index + 1,
                    'pertanyaan' => $soal['pertanyaan'] ?? '',
                    'pilihan' => $soal['pilihan'] ?? [],
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0)
                ];
            }
            $soalPosttest = $soalFormatted;
        }

        // Tentukan passing grade
        $passingGrade = 0;
        if (in_array('pretest', $request->content_types)) {
            $passingGrade = $request->passing_grade_pretest;
        } elseif (in_array('posttest', $request->content_types)) {
            $passingGrade = $request->passing_grade_posttest;
        }

        // Simpan data dengan nilai default untuk menghindari NULL
        $materialData = [
            'course_id' => $kursus->id,
            'title' => $request->title,
            'description' => $request->description ?? '',
            'order' => $request->order,
            'type' => $type,
            'material_type' => $materialType, // sementara gunakan single value
            'duration' => $duration,
            'file_path' => $filePath,
            'video_url' => $request->video_url ?? '',
            'is_active' => $request->has('is_active'),
            'attendance_required' => $request->has('attendance_required'),
            'durasi_pretest' => in_array('pretest', $request->content_types) ? $request->durasi_pretest : 0,
            'durasi_posttest' => in_array('posttest', $request->content_types) ? $request->durasi_posttest : 0,
            'passing_grade' => $passingGrade,
            'soal_pretest' => $soalPretest,
            'soal_posttest' => $soalPosttest,
            'is_pretest' => in_array('pretest', $request->content_types),
            'is_posttest' => in_array('posttest', $request->content_types),
        ];

        // Simpan content_types sebagai JSON di field learning_objectives sementara
        $materialData['learning_objectives'] = json_encode($request->content_types);

        // Debug data sebelum disimpan
        Log::info('Material Data to be stored:', $materialData);

        try {
            Materials::create($materialData);
            
            return redirect()->route('admin.kursus.materials.index', $kursus)
                            ->with('success', 'Material berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing material: ' . $e->getMessage());
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menyimpan material: ' . $e->getMessage());
        }
    }

    public function edit(Kursus $kursus, Materials $material)
    {
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
        ]);

        // Conditional validasi untuk file
        if (in_array('file', $request->content_types)) {
            $request->validate([
                'file_path' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
                'duration_file' => 'required|integer|min:1',
            ]);
        } else {
            $request->merge(['duration_file' => 0]);
        }

        // Conditional validasi untuk video
        if (in_array('video', $request->content_types)) {
            $request->validate([
                'video_url' => 'required|url',
                'duration_video' => 'required|integer|min:1',
            ]);
        } else {
            $request->merge([
                'video_url' => '',
                'duration_video' => 0,
            ]);
        }

        // Conditional validasi untuk pretest
        if (in_array('pretest', $request->content_types)) {
            $request->validate([
                'durasi_pretest' => 'required|integer|min:1',
                'passing_grade_pretest' => 'required|integer|min:1|max:100',
                'pretest_soal' => 'required|array|min:1',
                'pretest_soal.*.pertanyaan' => 'required|string',
                'pretest_soal.*.pilihan' => 'required|array|min:2',
                'pretest_soal.*.pilihan.*' => 'required|string',
                'pretest_soal.*.jawaban_benar' => 'required|integer|min:0',
            ]);
        } else {
            $request->merge([
                'durasi_pretest' => 0,
                'passing_grade_pretest' => 0,
                'pretest_soal' => [],
            ]);
        }

        // Conditional validasi untuk posttest
        if (in_array('posttest', $request->content_types)) {
            $request->validate([
                'durasi_posttest' => 'required|integer|min:1',
                'passing_grade_posttest' => 'required|integer|min:1|max:100',
                'posttest_soal' => 'required|array|min:1',
                'posttest_soal.*.pertanyaan' => 'required|string',
                'posttest_soal.*.pilihan' => 'required|array|min:2',
                'posttest_soal.*.pilihan.*' => 'required|string',
                'posttest_soal.*.jawaban_benar' => 'required|integer|min:0',
            ]);
        } else {
            $request->merge([
                'durasi_posttest' => 0,
                'passing_grade_posttest' => 0,
                'posttest_soal' => [],
            ]);
        }

        // Handle file upload
        $filePath = $material->file_path;
        if (in_array('file', $request->content_types) && $request->hasFile('file_path')) {
            // Hapus file lama jika ada
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $filePath = $request->file('file_path')->store('materials', 'public');
        } elseif (!in_array('file', $request->content_types)) {
            // Jika file tidak dipilih, hapus file yang ada
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $filePath = null;
        }

        // Hitung total durasi dari file dan video
        $duration = 0;
        if (in_array('file', $request->content_types)) {
            $duration += $request->duration_file;
        }
        if (in_array('video', $request->content_types)) {
            $duration += $request->duration_video;
        }

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
        } elseif (in_array('pretest', $request->content_types)) {
            $materialType = 'quiz';
        } elseif (in_array('posttest', $request->content_types)) {
            $materialType = 'quiz';
        }

        // Format soal pretest
        $soalPretest = null;
        if (in_array('pretest', $request->content_types) && !empty($request->pretest_soal)) {
            $soalFormatted = [];
            foreach ($request->pretest_soal as $index => $soal) {
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
                $soalFormatted[] = [
                    'id' => $index + 1,
                    'pertanyaan' => $soal['pertanyaan'] ?? '',
                    'pilihan' => $soal['pilihan'] ?? [],
                    'jawaban_benar' => (int)($soal['jawaban_benar'] ?? 0)
                ];
            }
            $soalPosttest = $soalFormatted;
        }

        // Tentukan passing grade
        $passingGrade = 0;
        if (in_array('pretest', $request->content_types)) {
            $passingGrade = $request->passing_grade_pretest;
        } elseif (in_array('posttest', $request->content_types)) {
            $passingGrade = $request->passing_grade_posttest;
        }

        // Update data
        $materialData = [
            'title' => $request->title,
            'description' => $request->description ?? '',
            'order' => $request->order,
            'type' => $type,
            'material_type' => $materialType,
            'duration' => $duration,
            'file_path' => $filePath,
            'video_url' => $request->video_url ?? '',
            'is_active' => $request->has('is_active'),
            'attendance_required' => $request->has('attendance_required'),
            'durasi_pretest' => in_array('pretest', $request->content_types) ? $request->durasi_pretest : 0,
            'durasi_posttest' => in_array('posttest', $request->content_types) ? $request->durasi_posttest : 0,
            'passing_grade' => $passingGrade,
            'soal_pretest' => $soalPretest,
            'soal_posttest' => $soalPosttest,
            'is_pretest' => in_array('pretest', $request->content_types),
            'is_posttest' => in_array('posttest', $request->content_types),
            'learning_objectives' => json_encode($request->content_types),
        ];

        // Debug data sebelum update
        Log::info('Material Data to be updated:', $materialData);

        try {
            $material->update($materialData);

            return redirect()->route('admin.kursus.materials.index', $kursus)
                            ->with('success', 'Material berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating material: ' . $e->getMessage());
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal memperbarui material: ' . $e->getMessage());
        }
    }

    public function destroy(Kursus $kursus, Materials $material)
    {
        try {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

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
}