<?php

namespace App\Services;

use App\Models\PendingList;
use App\Models\Template;
use App\Repositories\Contracts\PendingListRepositoryInterface;
use App\Repositories\Contracts\SmsHistoryRepositoryInterface;
use App\Repositories\Contracts\SmsQueueRepositoryInterface;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use App\Services\Contracts\SmsServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Excel;



class SmsService implements SmsServiceInterface
{
    /**
     * @var PendingListRepositoryInterface
     */
    protected $pendingListRepository;

    /**
     * @var SmsQueueRepositoryInterface
     */
    protected $smsQueueRepository;

    /**
     * @var SmsHistoryRepositoryInterface
     */
    protected $smsHistoryRepository;

    /**
     * @var TemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * SmsService constructor.
     *
     * @param PendingListRepositoryInterface $pendingListRepository
     * @param SmsQueueRepositoryInterface $smsQueueRepository
     * @param SmsHistoryRepositoryInterface $smsHistoryRepository
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        PendingListRepositoryInterface $pendingListRepository,
        SmsQueueRepositoryInterface $smsQueueRepository,
        SmsHistoryRepositoryInterface $smsHistoryRepository,
        TemplateRepositoryInterface $templateRepository
    ) {
        $this->pendingListRepository = $pendingListRepository;
        $this->smsQueueRepository = $smsQueueRepository;
        $this->smsHistoryRepository = $smsHistoryRepository;
        $this->templateRepository = $templateRepository;
    }

    /**
     * Send SMS to all users
     *
     * @param string $message
     * @param string $templateName
     * @return bool
     */
    public function sendToAllUsers(string $message, string $templateName,string $authorizerName): bool
    {
        // Get the template from database first
        $template = $this->templateRepository->findByName($templateName)->where('approval_status', 'approved')->first();

        if (!$template) {
            return false;
        }

        // Create a record in the pending_list table with template_id only
        $pendingRecord = $this->pendingListRepository->create([
            'template_id' => $template->id,
            'message' => $message,
            'status' => 'pending',
            'timestamp' => now(),
        ]);

        // Log to SMS history
        $this->smsHistoryRepository->create([
            'template_id' => $template->id,
            'recipient' => 'All Users',
            'mobile_no' => null, // No specific mobile_no for "all users"
            'template_name' => $template->name,
            'status' => 'pending',
            'message' => $message,
            'authorizer' => $authorizerName, // Use the provided message from SMS form
            'modified_at' => now(),
            'source' => 'All Users'
        ]);

        return true;
    }

    /**
     * Send SMS to comma-separated list of numbers
     *
     * @param string $numbers
     * @param string $message
     * @param string $templateName
     * @return bool
     */
    public function sendToCommaSeparatedNumbers(string $numbers, string $message, string $templateName,string $authorizerName): bool
    {
        // Split the comma-separated string into an array of numbers
        $numberArray = explode(',', $numbers);
        $trimmedNumbers = array_map('trim', $numberArray);

        // Get the template
        $template = $this->templateRepository->findByName($templateName)->where('approval_status', 'approved')->first();

        if (!$template) {
            return false;
        }

        // Create a record in the pending_list table
        $pendingRecord = $this->pendingListRepository->create([
            'template_id' => $template->id,
            'message' => $message,
            'authorizer' => $authorizerName,
            'status' => 'pending',
            'timestamp' => now(),
        ]);

        // Insert each number into the smsqueue table
        foreach ($trimmedNumbers as $mobile) {
            if (empty($mobile)) {
                continue;
            }

            // Add to queue
            $this->smsQueueRepository->create([
                'mobile' => $mobile,
                'msg' => $message,
                'excel_id'=> $pendingRecord->id,
                'refid' => null,
                'status' => 'pending',
                'timestamp' => now(),
            ]);

            // Add to history
            $this->smsHistoryRepository->create([
                'template_id' => $template->id,
                'recipient' => 'Direct Entry',
                'mobile_no' => $mobile,
                'template_name' => $template->name,
                'status' => 'pending',
                'message' => $message,
                'authorizer' => $authorizerName,
                'modified_at' => now(),
                'source' => 'Comma-Separated Numbers'
            ]);
        }

        return true;
    }

    /**
     * Send SMS from an uploaded Excel/CSV file
     *
     * @param UploadedFile $file
     * @param string $message
     * @param string $templateName
     * @return bool
     */
 

public function sendFromExcel(UploadedFile $file, string $message, string $templateName, string $authorizerName): bool
{

    Log::info("sendFromExcel called", [
    'filename' => $file->getClientOriginalName(),
    'time' => now()
]);
    DB::enableQueryLog(); // Enable query logging

    try {
        if (!$file->isValid()) {
            throw new \Exception("Invalid file: " . $file->getErrorMessage());
        }

        $template = $this->templateRepository
            ->findByName($templateName)
            ->where('approval_status', 'approved')
            ->first();

        if (!$template) {
            throw new \Exception("Template not approved or not found");
        }

        $excelRecord = Excel::firstOrCreate(
    ['name' => $file->getClientOriginalName()],
    ['created_at' => now(), 'updated_at' => now()]
);

        return DB::transaction(function () use ($file, $message, $template, $authorizerName,$excelRecord) {
            $pendingRecord = $this->pendingListRepository->create([
                'template_id' => $template->id,
                'message' => $message,
                'authorizer' => $authorizerName,
                'original_filename' => $file->getClientOriginalName(),
                'status' => 'pending',
                'timestamp' => now(),
                'excel_id' => $excelRecord->id,
            ]);

            if (!$pendingRecord) {
                throw new \Exception("Failed to create pending record");
            }

            $result = $this->processSmsFile($pendingRecord, $file, $message, $authorizerName);
            
            // Debug: Log all executed queries
            Log::debug("Database queries executed:", DB::getQueryLog());
            
            return $result;
        });

    } catch (\Exception $e) {
    Log::error("Excel processing failed", [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'file' => $file->getClientOriginalName(),
        'queries' => DB::getQueryLog() // Add this to see executed queries
    ]);
    throw $e; // Re-throw to let caller handle it
}
}

    /**
     * Process an SMS file to extract numbers and send messages
     *
     * @param PendingList $pendingRecord
     * @param string $message
     * @return bool
     */
  


public function processSmsFile(PendingList $pendingRecord, UploadedFile $file, string $message, string $authorizerName): bool
{
    try {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            throw new \Exception("Could not open file");
        }

        // Read headers and validate
        $headers = array_map('trim', fgetcsv($handle));
        
        // Find mobile and name columns
        $mobileColumnName = $this->findColumn($headers, ['mobile', 'phone', 'number']);
        $nameColumnName = $this->findColumn($headers, ['name', 'applicant name', 'applicant']);
        
        if (!$mobileColumnName) {
            throw new \Exception("Missing required mobile number column");
        }
        
        if (!$nameColumnName) {
            throw new \Exception("Missing required name column");
        }

        // Find placeholder columns (p1, p2, etc.)
        $placeholderColumns = [];
        foreach ($headers as $header) {
            if (preg_match('/^p\d+$/i', $header)) { // Matches p1, p2, etc.
                $placeholderColumns[] = $header;
            }
        }

        $queueItems = [];
        $processedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) continue;

            $rowData = array_combine($headers, $row);
            $mobile = preg_replace('/[^0-9]/', '', $rowData[$mobileColumnName] ?? '');

            if (empty($mobile) || strlen($mobile) < 10) {
                Log::warning("Invalid mobile number", ['number' => $mobile]);
                continue;
            }

            // Prepare data for personalization
            $personalizationData = [];
            foreach ($placeholderColumns as $col) {
                $personalizationData[$col] = $rowData[$col] ?? '';
            }

            // Personalize the message
            $personalized = $this->personalizeMessage($message, $personalizationData);

            // Prepare queue item
            $queueItems[] = [
                'mobile' => $mobile,
                'msg' => $personalized, // Use the personalized message here
                'excel_id' => $pendingRecord->id,
                'refid' => null,
                'status' => 'pending',
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Create history record
            $historyData = [
                'template_id' => $pendingRecord->template_id,
                'recipient' => $rowData[$nameColumnName] ?? '',
                'mobile_no' => $mobile,
                'template_name' => $pendingRecord->template->name,
                'message' => $personalized, // Use the same personalized message here
                'authorizer' => $authorizerName,
                'status' => 'pending',
                'modified_at' => now(),
                'source' => 'Excel: ' . $pendingRecord->original_filename,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (!$this->smsHistoryRepository->create($historyData)) {
                throw new \Exception("Failed to create history record");
            }

            $processedCount++;
        }
        fclose($handle);

        // Bulk insert with error handling
        if (!empty($queueItems) && !$this->smsQueueRepository->bulkCreate($queueItems)) {
            throw new \Exception("Failed to insert queue items");
        }

        Log::info("Successfully processed {$processedCount} records");
        return true;

    } catch (\Exception $e) {
        Log::error("File processing failed", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

// Helper function to find columns
private function findColumn(array $headers, array $possibleNames): ?string
{
    foreach ($headers as $header) {
        $lowerHeader = strtolower($header);
        foreach ($possibleNames as $name) {
            if (strpos($lowerHeader, strtolower($name)) !== false) {
                return $header;
            }
        }
    }
    return null;
}
    /**
     * Log SMS history for a pending record
     *
     * @param PendingList $pendingRecord
     * @param string $status
     * @return void
     */
    public function logSmsHistory(PendingList $pendingRecord, string $status,$authorizerName): void
    {
        // Skip if file was uploaded and already processed
        if ($pendingRecord->file_path && $status === 'sent') {
            return;
        }

        // Don't create summary records for file uploads - only create individual records
        if ($pendingRecord->file_path) {
            return;
        }

        // Prepare recipient summary
        $recipient = '';

        if ($pendingRecord->file_path) {
            // For file uploads, show the filename and count in the summary
            $filePath = storage_path('app/public/' . $pendingRecord->file_path);
            $extractedCount = 0;

            if (file_exists($filePath)) {
                $data = array_map('str_getcsv', file($filePath));
                // Subtract 1 for header row
                $extractedCount = count($data) - 1;
            }

            $recipient = $pendingRecord->original_filename . ' (' . $extractedCount . ' numbers)';
        } else {
            // For direct numbers, count them
            $smsRecords = $this->smsQueueRepository->countByRefId($pendingRecord->id);
            $recipient = $smsRecords . ' recipients';
        }

        // Get the template from the relationship
        $template = $pendingRecord->template;

        // Log to SMS history
        if ($template) {
            // Determine the appropriate source based on the method
            $source = 'Direct Entry';
            if ($pendingRecord->file_path) {
                  $source = 'Excel: ' . $pendingRecord->original_filename; 
            }

            // Map status to one of the allowed values in the sms_history table
            $historyStatus = $status;
            if ($status === 'approved' || $status === 'rejected' || $status === 'cancelled') {
                // Map to one of the allowed statuses in the enum ('pending', 'sent', 'rejected', 'failed')
                $historyStatus = $status === 'approved' ? 'sent' : ($status === 'rejected' ? 'rejected' : 'failed');
            }

            $this->smsHistoryRepository->create([
                'template_id' => $template->id,
                'recipient' => $recipient,
                'mobile_no' => null, // No mobile_no for summary records
                'template_name' => $template->name,
                'message' => $template->message, // Store the template message
                'status' => $historyStatus,
                'modified_at' => now(),
                'source' => $source
            ]);
        }
    }

    /**
     * Get all pending SMS records
     *
     * @return Collection
     */
    public function getWaitingList(): Collection
    {
        return $this->pendingListRepository->getByStatus('pending');
    }

    /**
     * Get paginated SMS records (pending status only)
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedWaitingList(int $perPage = 10): LengthAwarePaginator
    {
        return $this->pendingListRepository->getPaginatedByStatus('pending', $perPage);
    }

    /**
     * Get paginated SMS records (all statuses)
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedAllRecords(int $perPage = 10): LengthAwarePaginator
    {
        return $this->pendingListRepository->getPaginatedAll($perPage);
    }

    /**
     * Update the status of a pending SMS
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateSmsStatus(int $id, string $status, string $authorizerName): bool
    {
        $pendingRecord = $this->pendingListRepository->find($id);

        if (!$pendingRecord) {
            return false;
        }

        // Update the status of the pending record
        $this->pendingListRepository->update($id, [
            'status' => $status,
            'timestamp' => now()
        ]);

        // For approved status, use the approveSms method
        if ($status === 'approved') {
            return $this->approveSms($id, 'pending_list');
        }

        // For other statuses, update both queue and history
        $this->smsQueueRepository->updateStatusByRefId($pendingRecord->id, $status);

        // Update related SMS history records using the same mechanism as smsQueueRepository
        $this->smsHistoryRepository->updateStatusByRefId($pendingRecord->id, $status);

        // Log the status change
        $this->logSmsHistory($pendingRecord, $status, $authorizerName);

        return true;
    }



    public function queueSms(?string $mobile = null, ?string $message = null, ?string $templateName = null, ?array $personalData = null): bool
{
    if (!$mobile || !$message) {
        return false; // must have mobile and message
    }

    // Find template by name if given
    $template = null;
    if ($templateName) {
        $template = $this->templateRepository->findByName($templateName)->where('approval_status', 'approved')->first();
        if (!$template) {
            return false;
        }
    }

    // Personalize message if personal data provided
    if ($personalData) {
        $message = $this->personalizeMessage($message, $personalData);
    }

    // Create pending record if template exists
    $pendingRecord = null;
    if ($template) {
        $pendingRecord = $this->pendingListRepository->create([
            'template_id' => $template->id,
            'message' => $message,
            'status' => 'pending',
            'timestamp' => now(),
        ]);
    }

    $pendingId = $pendingRecord ? $pendingRecord->id : null;

    // Insert into sms queue
    $this->smsQueueRepository->create([
        'mobile' => $mobile,
        'msg' => $message,
        'excel_id' => $pendingId,
        'refid' => null,
        'status' => 'pending',
        'timestamp' => now(),
    ]);

    // Log to SMS history
    $this->smsHistoryRepository->create([
        'template_id' => $template ? $template->id : null,
        'recipient' => 'Direct Entry',
        'mobile_no' => $mobile,
        'template_name' => $template ? $template->name : '',
        'status' => 'pending',
        'message' => $message,
        'authorizer' => '',  // You can add authorizer param if you want, else keep empty or modify interface
        'modified_at' => now(),
        'source' => 'Queue SMS',
    ]);

    return true;
}

    

    /**
     * Personalize message with data
     *
     * @param string $message
     * @param array $data
     * @return string
     */
    public function personalizeMessage(string $message, array $data): string
{
    $personalizedMessage = $message;

    // Replace placeholders #p1#, #p2#, ..., #p10# with column values
    for ($i = 1; $i <= 10; $i++) {
        $placeholder = "#p{$i}#";
        $value = $data["p{$i}"] ?? '';
        $personalizedMessage = str_replace($placeholder, $value, $personalizedMessage);
    }

    return $personalizedMessage;
}

    /**
     * Approve SMS records by ID, updating status instead of creating new records
     *
     * @param int $id ID of the pending record or template ID
     * @param string $type Type of approval ('pending_list', 'template', 'all')
     * @return bool
     */
    public function approveSms(int $id, string $type = 'pending_list'): bool
    {
        switch ($type) {
            case 'pending_list':
                // Approve SMS by pending list ID
                $pendingRecord = $this->pendingListRepository->find($id);

                if (!$pendingRecord) {
                    return false;
                }

                // Update pending record status
                $this->pendingListRepository->update($id, [
                    'status' => 'approved',
                    'timestamp' => now()
                ]);

                // Update all related SMS queue records
                $this->smsQueueRepository->updateStatusByRefId($id, 'approved');

                // Update all related SMS history records using the same mechanism as smsQueueRepository
                $this->smsHistoryRepository->updateStatusByRefId($id, 'sent');

                return true;

            case 'template':
                // Approve all SMS records with this template ID
                $this->smsHistoryRepository->updateStatus([
                    'template_id' => $id,
                    'status' => 'pending'
                ], 'approved');

                return true;

            case 'all':
                // Approve all pending SMS records (use with caution)
                $this->smsHistoryRepository->updateStatus([
                    'status' => 'pending'
                ], 'approved');

                return true;

            default:
                return false;
        }
    }
}
