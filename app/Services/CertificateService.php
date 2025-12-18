<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\Kursus;
use App\Models\M_User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Generate certificate number format: No. XXX/MOOC/BPS.TanahLaut/YYYY
     */
    public function generateCertificateNumber(Enrollment $enrollment): string
    {
        $year = date('Y');
        $sequence = Certificate::whereYear('issued_at', $year)->count() + 1;
        $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return "No. {$formattedSequence}/MOOC/BPS.TanahLaut/{$year}";
    }

    /**
     * Generate certificate PDF and save to storage
     */
    public function generateCertificatePDF(Certificate $certificate)
    {
        // Load data dengan relasi
        $certificate->load(['user', 'kursus', 'enrollment']);
        
        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'kursus' => $certificate->kursus,
            'enrollment' => $certificate->enrollment,
        ];
        
        // Generate PDF
        $pdf = Pdf::loadView('mitra.sertifikat.template', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Times New Roman'
            ]);
        
        // Simpan ke storage
        $filename = "certificate_{$certificate->certificate_number}.pdf";
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
     * Create certificate for enrollment
     */
    public function createCertificate(Enrollment $enrollment): ?Certificate
    {
        // Pastikan enrollment sudah selesai
        if ($enrollment->status !== 'completed') {
            return null;
        }
        
        // Cek apakah sertifikat sudah ada
        if ($enrollment->certificate) {
            return $enrollment->certificate;
        }
        
        // Generate nomor sertifikat
        $certificateNumber = $this->generateCertificateNumber($enrollment);
        
        // Buat record sertifikat
        $certificate = Certificate::create([
            'certificate_number' => $certificateNumber,
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'kursus_id' => $enrollment->kursus_id,
            'issued_at' => now(),
        ]);
        
        // Generate PDF
        $this->generateCertificatePDF($certificate);
        
        return $certificate;
    }

    /**
     * Check enrollment progress and issue certificate if completed
     */
    public function checkAndIssueCertificate(Enrollment $enrollment): ?Certificate
    {
        // Jika progress 100% dan status belum completed, update status
        if ($enrollment->progress_percentage >= 100 && $enrollment->status !== 'completed') {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
        
        // Jika status completed, buat sertifikat
        if ($enrollment->status === 'completed') {
            return $this->createCertificate($enrollment);
        }
        
        return null;
    }

    /**
     * Bulk check for all enrollments that are 100% but no certificate
     */
    public function checkPendingCertificates()
    {
        $enrollments = Enrollment::where('progress_percentage', '>=', 100)
            ->where('status', 'completed')
            ->whereDoesntHave('certificate')
            ->get();
        
        $certificates = [];
        
        foreach ($enrollments as $enrollment) {
            $certificates[] = $this->createCertificate($enrollment);
        }
        
        return $certificates;
    }
}