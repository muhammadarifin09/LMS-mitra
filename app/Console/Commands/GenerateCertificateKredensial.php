<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CertificateService;

class GenerateCertificateKredensial extends Command
{
    protected $signature = 'certificates:generate-kredensial {--all : Generate for all certificates}';
    protected $description = 'Generate id_kredensial for certificates';
    
    protected $certificateService;
    
    public function __construct(CertificateService $certificateService)
    {
        parent::__construct();
        $this->certificateService = $certificateService;
    }
    
    public function handle()
    {
        if ($this->option('all')) {
            $results = $this->certificateService->regenerateIdKredensialForAll();
            
            $this->table(
                ['ID', 'Kredensial', 'User', 'Kursus'],
                $results
            );
            
            $this->info(count($results) . ' certificates updated.');
        } else {
            $this->info('Use --all option to generate for all certificates');
        }
    }
}