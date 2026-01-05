<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Storage;

class RegenerateCertificatesWithKredensial extends Command
{
    protected $signature = 'certificates:regenerate-pdf 
                            {--id= : Regenerate specific certificate ID}
                            {--all : Regenerate all certificates}
                            {--dry-run : Show what would be regenerated}';
    
    protected $description = 'Regenerate PDF certificates to include id_kredensial';
    
    protected $certificateService;
    
    public function __construct(CertificateService $certificateService)
    {
        parent::__construct();
        $this->certificateService = $certificateService;
    }
    
    public function handle()
    {
        $certificates = $this->getCertificates();
        
        if ($this->option('dry-run')) {
            $this->info('DRY RUN - Certificates to regenerate:');
            $this->table(
                ['ID', 'Certificate Number', 'User', 'Course', 'Has PDF'],
                $certificates->map(function ($cert) {
                    return [
                        $cert->id,
                        $cert->certificate_number,
                        $cert->user->nama ?? 'N/A',
                        $cert->kursus->judul_kursus ?? 'N/A',
                        $cert->file_path ? '✓' : '✗'
                    ];
                })
            );
            return;
        }
        
        $bar = $this->output->createProgressBar(count($certificates));
        $bar->start();
        
        $regenerated = 0;
        $failed = 0;
        
        foreach ($certificates as $certificate) {
            try {
                // Hapus file PDF lama jika ada
                if ($certificate->file_path && Storage::exists($certificate->file_path)) {
                    Storage::delete($certificate->file_path);
                }
                
                // Generate ulang PDF dengan id_kredensial
                $this->certificateService->generateCertificatePDF($certificate);
                
                $regenerated++;
                
            } catch (\Exception $e) {
                $this->error("Failed to regenerate certificate {$certificate->id}: " . $e->getMessage());
                $failed++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("✅ Successfully regenerated: {$regenerated} certificates");
        if ($failed > 0) {
            $this->error("❌ Failed: {$failed} certificates");
        }
    }
    
    private function getCertificates()
    {
        if ($this->option('id')) {
            return Certificate::where('id', $this->option('id'))->get();
        }
        
        if ($this->option('all')) {
            return Certificate::with(['user', 'kursus'])->get();
        }
        
        $this->error('Please specify --id or --all option');
        return collect();
    }
}