<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\Kursus;
use App\Models\M_User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    public function generateIdKredensial(): string
    {
        do {
            // Generate 8 karakter random (huruf besar dan angka)
            $id_kredensial = Str::upper(Str::random(8));
            
            // Pastikan hanya mengandung huruf besar dan angka
            if (!preg_match('/^[A-Z0-9]+$/', $id_kredensial)) {
                continue;
            }
            
            // Cek apakah sudah ada di database
        } while (Certificate::where('id_kredensial', $id_kredensial)->exists());
        
        return $id_kredensial;
    }

    /**
     * Generate certificate PDF and save to storage
     */
    public function generateCertificatePDF(Certificate $certificate)
    {
        try {
            // Load data dengan relasi
            $certificate->load(['user', 'kursus', 'enrollment']);
            
            $qrPath = $this->generateQRCode($certificate->id_kredensial);

            $data = [
                'certificate' => $certificate,
                'user' => $certificate->user,
                'kursus' => $certificate->kursus,
                'enrollment' => $certificate->enrollment,
                'qrPath' => $qrPath,
            ];
            
            // Generate PDF
            $pdf = Pdf::loadView('mitra.sertifikat.template', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Times New Roman',
                ]);
            
            // Simpan ke storage
            $rawName = "certificate_{$certificate->certificate_number}.pdf";
                $filename = preg_replace('/[\/\\\\:*?"<>|]/', '_', $rawName);

                $path = "certificates/{$certificate->user_id}/{$filename}";

                Storage::disk('private')->put($path, $pdf->output());

                $certificate->update([
                    'file_path' => $path,
                ]);

            // Update path di database
            $certificate->update([
                'file_path' => $path,
                'download_url' => route('sertifikat.download', $certificate),
            ]);
            
            return $pdf;

        } catch (\Exception $e) {
            Log::error('Error generating certificate PDF', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
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
        
        // Generate id_kredensial
        $idKredensial = $this->generateIdKredensial();
        
        // Buat record sertifikat
        $certificate = Certificate::create([
            'certificate_number' => $certificateNumber,
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'kursus_id' => $enrollment->kursus_id,
            'id_kredensial' => $idKredensial, // Tambahkan ini
            'issued_at' => now(),
        ]);
        
        // Generate PDF
        $this->generateCertificatePDF($certificate);
        
        Log::info('Certificate created', [
            'certificate_id' => $certificate->id,
            'user_id' => $enrollment->user_id,
            'kursus_id' => $enrollment->kursus_id,
            'id_kredensial' => $idKredensial,
        ]);
        
        return $certificate;
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

    /**
     * Regenerate id_kredensial for existing certificates (optional)
     */
    public function regenerateIdKredensialForAll(): array
    {
        $certificates = Certificate::whereNull('id_kredensial')->get();
        $results = [];
        
        foreach ($certificates as $certificate) {
            $idKredensial = $this->generateIdKredensial();
            $certificate->update(['id_kredensial' => $idKredensial]);
            $results[] = [
                'certificate_id' => $certificate->id,
                'id_kredensial' => $idKredensial,
                'user' => $certificate->user->nama ?? 'N/A',
                'kursus' => $certificate->kursus->judul_kursus ?? 'N/A',
            ];
        }
        
        return $results;
    }

    /**
     * Get certificate by id_kredensial
     */
    public function getCertificateByKredensial(string $idKredensial): ?Certificate
    {
        return Certificate::where('id_kredensial', $idKredensial)->first();
    }

    public function generateQRCode($idKredensial, $size = 150)
    {
        $verificationUrl = url('/sertifikat/' . $idKredensial);

        $path = "qrcodes/{$idKredensial}.svg";

        if (!Storage::disk('public')->exists($path)) {
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size($size)
                ->generate(
                    $verificationUrl,
                    storage_path("app/public/{$path}")
                );
        }

        // 🔥 Baca SVG lalu ubah ke data URI
        $svg = file_get_contents(storage_path("app/public/{$path}"));

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }




    /**
     * Check enrollment progress and issue certificate if completed
     */
    // public function checkAndIssueCertificate(Enrollment $enrollment): ?Certificate
    // {
    //     // Jika progress 100% dan status belum completed, update status
    //     if ($enrollment->progress_percentage >= 100 && $enrollment->status !== 'completed') {
    //         $enrollment->update([
    //             'status' => 'completed',
    //             'completed_at' => now(),
    //         ]);
    //     }
        
    //     // Jika status completed, buat sertifikat
    //     if ($enrollment->status === 'completed') {
    //         return $this->createCertificate($enrollment);
    //     }
        
    //     return null;
    // }

    /**
     * Create certificate for completed enrollment
     */
    // public function createCertificateRecord(Enrollment $enrollment): ?Certificate
    // {
    //     try {
    //         // Pastikan enrollment sudah completed
    //         if ($enrollment->status !== 'completed') {
    //             Log::warning('Enrollment not completed, cannot create certificate', [
    //                 'enrollment_id' => $enrollment->id,
    //                 'status' => $enrollment->status,
    //             ]);
    //             return null;
    //         }

    //         // Cek apakah sudah ada sertifikat
    //         if ($enrollment->certificate) {
    //             Log::info('Certificate already exists for enrollment', [
    //                 'enrollment_id' => $enrollment->id,
    //                 'certificate_id' => $enrollment->certificate->id,
    //             ]);
    //             return $enrollment->certificate;
    //         }

    //         // Generate certificate number
    //         $certificateNumber = $this->generateCertificateNumber($enrollment);
            
    //         Log::info('Creating certificate record', [
    //             'enrollment_id' => $enrollment->id,
    //             'certificate_number' => $certificateNumber,
    //         ]);

    //         // Buat record sertifikat
    //         $certificate = Certificate::create([
    //             'certificate_number' => $certificateNumber,
    //             'enrollment_id' => $enrollment->id,
    //             'user_id' => $enrollment->user_id,
    //             'kursus_id' => $enrollment->kursus_id,
    //             'issued_at' => now(),
    //         ]);

    //         // Generate PDF (bisa di-queue nanti)
    //         try {
    //             $this->generateCertificatePDF($certificate);
    //             Log::info('PDF generated for certificate', [
    //                 'certificate_id' => $certificate->id,
    //             ]);
    //         } catch (\Exception $e) {
    //             Log::error('Failed to generate PDF, but certificate record created', [
    //                 'certificate_id' => $certificate->id,
    //                 'error' => $e->getMessage(),
    //             ]);
    //         }

    //         return $certificate;

    //     } catch (\Exception $e) {
    //         Log::error('Error creating certificate record', [
    //             'enrollment_id' => $enrollment->id,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return null;
    //     }
    // }
}