<?php

namespace App\Http\Controllers\Mitra;

use App\Models\Certificate;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\CertificateService;

class CertificateController extends Controller
{
    /**
     * Display a listing of the user's certificates.
     */

    use AuthorizesRequests;

    protected $certificateService;
    
    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index(Request $request)
    {
        $user   = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $certificates = Certificate::with('kursus')
            ->where('user_id', $user->id)
            ->when($search, function ($q) use ($search) {
                $q->whereHas('kursus', function ($k) use ($search) {
                    $k->where('judul_kursus', 'like', "%{$search}%")
                    ->orWhere('pelaksana', 'like', "%{$search}%");
                });
            })
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query()); // penting agar search & per_page ikut ke pagination

        return view('mitra.sertifikat.index', compact('certificates'));
    }

    /**
     * Download the certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        // Manual authorization tanpa policy
        if (Auth::id() !== $certificate->user_id) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini.');
        }
        
        // Jika file belum ada, generate terlebih dahulu
        if (!$certificate->file_path || !Storage::exists($certificate->file_path)) {
            $this->certificateService->generateCertificatePDF($certificate);
        }
        
        $judulKursus = $certificate->kursus->judul_kursus;
        $safeJudul = preg_replace('/[^A-Za-z0-9\-]/', '_', $judulKursus);
        $filename = "Sertifikat-{$safeJudul}.pdf";
        
        return Storage::download($certificate->file_path, $filename);
    }

    /**
     * Generate PDF certificate.
     */
    private function generatePDF(Certificate $certificate)
    {
        // Load data dengan relasi
        $certificate->load(['user', 'kursus', 'enrollment']);
        
        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'kursus' => $certificate->kursus,
            'enrollment' => $certificate->enrollment,
        ];
        
        // Generate PDF menggunakan template
        $pdf = Pdf::loadView('mitra.sertifikat.template', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial MT Pro',
                'chroot' => public_path(),
            ]);
        
        // Simpan ke storage
        $rawName = "certificate_{$certificate->certificate_number}.pdf";

        // HAPUS SEMUA KARAKTER TERLARANG FILESYSTEM
        $filename = preg_replace('/[\/\\\\:*?"<>|]/', '_', $rawName);
        $path = "certificates/{$certificate->user_id}/{$filename}";
        
        Storage::put($path, $pdf->output());
        
        // Update path di database
        $certificate->update([
            'file_path' => $path,
            'download_url' => route('sertifikat.download', $certificate),
        ]);
        
        return $pdf;
    }

    /**
     * Check if user can download certificate for a course.
     */
    public function checkCertificate($kursusId)
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursusId)
            ->where('status', 'completed')
            ->first();
        
        if (!$enrollment) {
            return response()->json([
                'available' => false,
                'message' => 'Anda belum menyelesaikan kursus ini.'
            ]);
        }
        
        $certificate = Certificate::where('enrollment_id', $enrollment->id)->first();
        
        if ($certificate) {
            return response()->json([
                'available' => true,
                'certificate_id' => $certificate->id,
                'download_url' => route('certificates.download', $certificate),
                'preview_url' => route('certificates.preview', $certificate)
            ]);
        }
        
        // Coba buat sertifikat jika belum ada
        $newCertificate = $this->certificateService->createCertificate($enrollment);
        
        if ($newCertificate) {
            return response()->json([
                'available' => true,
                'certificate_id' => $newCertificate->id,
                'download_url' => route('certificates.download', $newCertificate),
                'preview_url' => route('certificates.preview', $newCertificate)
            ]);
        }
        
        return response()->json([
            'available' => false,
            'message' => 'Sertifikat belum tersedia.'
        ]);
    }

    public function validateCertificate($id_kredensial)
    {
        $certificate = Certificate::with(['user', 'kursus'])
            ->where('id_kredensial', $id_kredensial)
            ->first();

        if (!$certificate) {
            return view('mitra.sertifikat.invalid');
        }

        // Jika file PDF belum ada → generate
        if (!$certificate->file_path || !Storage::exists($certificate->file_path)) {
            $this->certificateService->generateCertificatePDF($certificate);
        }

        return view('mitra.sertifikat.validate', [
            'certificate' => $certificate
        ]);
    }

    public function publicPdf($id_kredensial)
    {
        $certificate = Certificate::where('id_kredensial', $id_kredensial)->firstOrFail();

        if (!$certificate->file_path || !Storage::disk('private')->exists($certificate->file_path)) {
            $this->certificateService->generateCertificatePDF($certificate);
        }

        return response()->file(
            storage_path('app/private/' . $certificate->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="certificate.pdf"',
            ]
        );
    }


}