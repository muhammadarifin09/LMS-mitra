<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Log;

class EnrollmentObserver
{
    protected $certificateService;
    
    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
        
    }
    
    /**
     * Handle the Enrollment "saved" event.
     */
    public function saved(Enrollment $enrollment): void
    {
        // Debug log
        Log::info('=== ENROLLMENT OBSERVER FIRED ===', [
            'id' => $enrollment->id,
            'progress' => $enrollment->progress_percentage,
            'status' => $enrollment->status,
            'has_cert' => $enrollment->certificate_id ? 'YES' : 'NO',
        ]);
        
        // Auto-update status jika progress 100%
        if ($enrollment->progress_percentage >= 100 && $enrollment->status !== 'completed') {
            Log::info('Auto-updating status to completed', [
                'enrollment_id' => $enrollment->id,
            ]);
            
            // Use query builder to avoid infinite loop
            Enrollment::where('id', $enrollment->id)->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            // Refresh model
            $enrollment->refresh();
        }
        
        // Buat sertifikat jika eligible
        if ($enrollment->progress_percentage >= 100 
            && $enrollment->status === 'completed' 
            && !$enrollment->certificate) {
            
            Log::info('Creating certificate for enrollment', [
                'enrollment_id' => $enrollment->id,
            ]);
            
            try {
                $certificate = $this->certificateService->createCertificate($enrollment);
                
                if ($certificate) {
                    Log::info('✅ Certificate created', [
                        'certificate_id' => $certificate->id,
                        'certificate_number' => $certificate->certificate_number,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to create certificate', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}