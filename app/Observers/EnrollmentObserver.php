<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Services\CertificateService;

class EnrollmentObserver
{
    protected $certificateService;
    
    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }
    
    /**
     * Handle the Enrollment "updated" event.
     */
    public function updated(Enrollment $enrollment): void
    {
        // Cek jika progress berubah
        if ($enrollment->isDirty('progress_percentage')) {
            $this->certificateService->checkAndIssueCertificate($enrollment);
        }
        
        // Jika status berubah menjadi completed
        if ($enrollment->isDirty('status') && $enrollment->status === 'completed') {
            $this->certificateService->createCertificate($enrollment);
        }
    }
    
    /**
     * Handle the Enrollment "updating" event.
     */
    public function updating(Enrollment $enrollment): void
    {
        // Otomatis update status ke completed jika progress 100%
        if ($enrollment->progress_percentage >= 100 && $enrollment->status !== 'completed') {
            $enrollment->status = 'completed';
            $enrollment->completed_at = now();
        }
    }
}