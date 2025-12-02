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

    // Conditional validasi untuk video
    if (in_array('video', $request->content_types)) {
        $request->validate([
            'video_url' => 'required|url',
            'duration_video' => 'required|integer|min:1',
        ]);
    }

    // Conditional validasi untuk pretest - DIHAPUS PASSING GRADE
    if (in_array('pretest', $request->content_types)) {
        $request->validate([
            'durasi_pretest' => 'required|integer|min:1',
            // HAPUS: 'passing_grade_pretest' => 'required|integer|min:1|max:100',
            'pretest_soal' => 'required|array|min:1',
            'pretest_soal.*.pertanyaan' => 'required|string',
            'pretest_soal.*.pilihan' => 'required|array|min:4|max:4',
            'pretest_soal.*.pilihan.*' => 'required|string',
            'pretest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
        ]);
    }

    // Conditional validasi untuk posttest - DIHAPUS PASSING GRADE
    if (in_array('posttest', $request->content_types)) {
        $request->validate([
            'durasi_posttest' => 'required|integer|min:1',
            // HAPUS: 'passing_grade_posttest' => 'required|integer|min:1|max:100',
            'posttest_soal' => 'required|array|min:1',
            'posttest_soal.*.pertanyaan' => 'required|string',
            'posttest_soal.*.pilihan' => 'required|array|min:4|max:4',
            'posttest_soal.*.pilihan.*' => 'required|string',
            'posttest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
        ]);
    }

    // Handle multiple file uploads
    $filePaths = [];
    if (in_array('file', $request->content_types) && $request->hasFile('file_path')) {
        foreach ($request->file('file_path') as $file) {
            $filePaths[] = $file->store('materials', 'public');
        }
    }

    // Hitung total durasi hanya dari video
    $duration = 0;
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
        'is_active' => $request->boolean('is_active'),
        'attendance_required' => $request->boolean('attendance_required'),
        // HAPUS: 'passing_grade' => $passingGrade,
        'soal_pretest' => $soalPretest,
        'soal_posttest' => $soalPosttest,
    ];

    // Tambahkan field durasi khusus jika ada
    if (in_array('pretest', $request->content_types)) {
        $materialData['durasi_pretest'] = $request->durasi_pretest;
    }
    if (in_array('posttest', $request->content_types)) {
        $materialData['durasi_posttest'] = $request->durasi_posttest;
    }
    if (in_array('video', $request->content_types)) {
        $materialData['duration_video'] = $request->duration_video;
    }

    // Simpan content_types sebagai JSON
    $materialData['learning_objectives'] = json_encode($request->content_types);

    // Debug data sebelum disimpan
    Log::info('Material Data to be stored:', $materialData);

    try {
        Materials::create($materialData);
        
        return redirect()->route('admin.kursus.materials.index', $kursus)
                        ->with('success', 'Material berhasil ditambahkan!');
    } catch (\Exception $e) {
        Log::error('Error storing material: ' . $e->getMessage());
        
        // Hapus file yang sudah diupload jika ada error
        foreach ($filePaths as $filePath) {
            Storage::disk('public')->delete($filePath);
        }
        
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

    // Conditional validasi untuk video
    if (in_array('video', $request->content_types)) {
        $request->validate([
            'video_url' => 'required|url',
            'duration_video' => 'required|integer|min:1',
        ]);
    }

    // Conditional validasi untuk pretest - DIHAPUS PASSING GRADE
    if (in_array('pretest', $request->content_types)) {
        $request->validate([
            'durasi_pretest' => 'required|integer|min:1',
            // HAPUS: 'passing_grade_pretest' => 'required|integer|min:1|max:100',
            'pretest_soal' => 'required|array|min:1',
            'pretest_soal.*.pertanyaan' => 'required|string',
            'pretest_soal.*.pilihan' => 'required|array|min:4|max:4',
            'pretest_soal.*.pilihan.*' => 'required|string',
            'pretest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
        ]);
    }

    // Conditional validasi untuk posttest - DIHAPUS PASSING GRADE
    if (in_array('posttest', $request->content_types)) {
        $request->validate([
            'durasi_posttest' => 'required|integer|min:1',
            // HAPUS: 'passing_grade_posttest' => 'required|integer|min:1|max:100',
            'posttest_soal' => 'required|array|min:1',
            'posttest_soal.*.pertanyaan' => 'required|string',
            'posttest_soal.*.pilihan' => 'required|array|min:4|max:4',
            'posttest_soal.*.pilihan.*' => 'required|string',
            'posttest_soal.*.jawaban_benar' => 'required|integer|min:0|max:3',
        ]);
    }

    // Handle multiple file uploads
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

    // Hitung total durasi hanya dari video
    $duration = 0;
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
        'is_active' => $request->boolean('is_active'),
        'attendance_required' => $request->boolean('attendance_required'),
        // HAPUS: 'passing_grade' => $passingGrade,
        'soal_pretest' => $soalPretest,
        'soal_posttest' => $soalPosttest,
        'learning_objectives' => json_encode($request->content_types),
    ];

    // Update field durasi khusus
    if (in_array('pretest', $request->content_types)) {
        $materialData['durasi_pretest'] = $request->durasi_pretest;
    } else {
        $materialData['durasi_pretest'] = null;
    }

    if (in_array('posttest', $request->content_types)) {
        $materialData['durasi_posttest'] = $request->durasi_posttest;
    } else {
        $materialData['durasi_posttest'] = null;
    }

    if (in_array('video', $request->content_types)) {
        $materialData['duration_video'] = $request->duration_video;
    } else {
        $materialData['duration_video'] = null;
    }

    // Debug data sebelum update
    Log::info('Material Data to be updated:', $materialData);

    try {
        $material->update($materialData);

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
        
        return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal memperbarui material: ' . $e->getMessage());
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