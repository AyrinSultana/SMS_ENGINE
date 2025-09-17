<?php

namespace App\Jobs;

use App\Models\PendingList;
use App\Services\Contracts\SmsServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The pending list record.
     *
     * @var PendingList
     */
    protected $pendingRecord;

    /**
     * Create a new job instance.
     *
     * @param PendingList $pendingRecord
     * @return void
     */
    public function __construct(PendingList $pendingRecord)
    {
        $this->pendingRecord = $pendingRecord;
    }

    /**
     * Execute the job.
     *
     * @param SmsServiceInterface $smsService
     * @return void
     */
    public function handle(SmsServiceInterface $smsService)
    {
        try {
            Log::info('Processing SMS job for Pending List ID: ' . $this->pendingRecord->id);
            
            // Determine the appropriate action based on the record type
            if ($this->pendingRecord->file_path) {
                // For file uploads, process the file but maintain the status as 'pending'
                // until approval
                $smsService->processSmsFile($this->pendingRecord);
            } else {
                // For direct numbers, do not update to 'sent' yet
                // Wait for approval
                // $smsService->updateSmsStatus($this->pendingRecord->id, 'sent');
                Log::info('SMS job awaiting approval for Pending List ID: ' . $this->pendingRecord->id);
            }
            
            Log::info('SMS job processed successfully for Pending List ID: ' . $this->pendingRecord->id);
        } catch (\Exception $e) {
            Log::error('Error processing SMS job: ' . $e->getMessage(), [
                'pending_id' => $this->pendingRecord->id,
                'exception' => $e
            ]);
            
            // Update the status to failed
            $this->pendingRecord->update(['status' => 'failed']);
            
            // Log the failure
            $smsService->logSmsHistory($this->pendingRecord, 'failed');
            
            // Throw the exception to trigger job failure
            throw $e;
        }
    }
}
