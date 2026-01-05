<?php

namespace App\Http\Controllers;
use App\Models\Nilai;
use App\Models\Enrollment;
use App\Services\NilaiService;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    protected $nilaiService;

    public function __construct(NilaiService $nilaiService)
    {
        $this->nilaiService = $nilaiService;
    }

    public function index()
    {
        $user = Auth::user();

        // Ambil kursus yang diikuti mitra
        $enrollments = Enrollment::with('kursus.materials')
            ->where('user_id', $user->id)
            ->get();

        $nilai = [];

        foreach ($enrollments as $enroll) {
            $kursus = $enroll->kursus;

            $nilaiAkhir = $this->nilaiService->hitungNilai($user->id, $kursus);
            $status     = $this->nilaiService->statusNilai($nilaiAkhir);

            $nilai[] = [
                'kursus' => $kursus,
                'nilai'  => $nilaiAkhir,
                'status' => $status,
            ];
        }

        return view('mitra.nilai.index', compact('nilai'));
    }

    public function simpan(NilaiService $nilaiService)
{
    $user = Auth::user();

    $enrollments = Enrollment::with('kursus.materials')
        ->where('user_id', $user->id)
        ->get();

    foreach ($enrollments as $enroll) {
        $kursus = $enroll->kursus;

        $nilaiAkhir = $nilaiService->hitungNilai($user->id, $kursus);
        $status     = $nilaiService->statusNilai($nilaiAkhir);

        // SIMPAN / UPDATE KE ARSIP
        Nilai::updateOrCreate(
            [
                'user_id'   => $user->id,
                'kursus_id' => $kursus->id,
            ],
            [
                'nilai'  => $nilaiAkhir,
                'status' => $status,
            ]
        );
    }

    return redirect()
        ->route('mitra.nilai')
        ->with('success', 'Nilai berhasil disimpan ke arsip.');
}
}
