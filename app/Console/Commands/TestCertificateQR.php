<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Storage; // ✅ Sudah di-import

class TestCertificateQR extends Command
{
    protected $signature = 'test:certificate-qr {id? : Certificate ID}';
    protected $description = 'Test QR Code generation for certificate';
    
    protected $certificateService;
    
    public function __construct(CertificateService $certificateService)
    {
        parent::__construct();
        $this->certificateService = $certificateService;
    }
    
    public function handle()
    {
        $certificateId = $this->argument('id');
        
        if ($certificateId) {
            $certificate = Certificate::find($certificateId);
            if (!$certificate) {
                $this->error("Certificate not found");
                return;
            }
            $certificates = collect([$certificate]);
        } else {
            $certificates = Certificate::whereNotNull('id_kredensial')
                ->limit(3)
                ->get();
        }
        
        foreach ($certificates as $certificate) {
            $this->info("\n=== Testing Certificate ID: {$certificate->id} ===");
            $this->info("Certificate Number: {$certificate->certificate_number}");
            $this->info("ID Kredensial: {$certificate->id_kredensial}");
            
            try {
                // Regenerate PDF
                $this->certificateService->generateCertificatePDF($certificate);
                $this->info("✅ PDF regenerated successfully");
                
                // Check file - PAKAI Storage TANPA backslash (karena sudah di-import)
                if ($certificate->file_path && Storage::exists($certificate->file_path)) {
                    $size = Storage::size($certificate->file_path);
                    $this->info("📄 File: {$certificate->file_path} (" . round($size/1024) . " KB)");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error: " . $e->getMessage());
            }
        }
    }
}