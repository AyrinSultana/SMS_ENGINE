<?php

namespace App\Services;

use App\Models\PendingList;
use App\Repositories\Contracts\SmsQueueRepositoryInterface;
use App\Repositories\Contracts\SmsHistoryRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ExcelProcessor
{
    protected $smsQueueRepository;
    protected $smsHistoryRepository;

    public function __construct(
        SmsQueueRepositoryInterface $smsQueueRepository,
        SmsHistoryRepositoryInterface $smsHistoryRepository
    ) {
        $this->smsQueueRepository = $smsQueueRepository;
        $this->smsHistoryRepository = $smsHistoryRepository;
    }

    public function process(
        string $filePath,
        PendingList $pendingRecord,
        string $message,
        string $authorizerName
    ): bool {
        try {
            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception("Could not open file");
            }

            // Read headers
            $headers = $this->normalizeHeaders(fgetcsv($handle));
            
            // Validate required columns
            if (!in_array('mobile number', $headers)) {
                throw new \Exception("CSV must contain 'Mobile Number' column");
            }

            $queueItems = [];
            $processedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($headers)) {
                    continue;
                }

                $rowData = array_combine($headers, $row);
                $mobile = trim($rowData['mobile number']);

                if (!$this->validateMobileNumber($mobile)) {
                    continue;
                }

                // Personalize message
                $personalizedMessage = $this->personalizeMessage($message, $rowData);

                $queueItems[] = [
                    'mobile' => $mobile,
                    'msg' => $personalizedMessage,
                    'excel_id' => $pendingRecord->id,
                    'status' => 'pending',
                    'timestamp' => now(),
                ];

                $this->smsHistoryRepository->create([
                    'template_id' => $pendingRecord->template_id,
                    'recipient' => $rowData['applicant name'] ?? '',
                    'mobile_no' => $mobile,
                    'template_name' => $pendingRecord->template->name,
                    'authorizer' => $authorizerName,
                    'status' => 'pending',
                    'message' => $personalizedMessage,
                    'source' => 'Excel Upload'
                ]);

                $processedCount++;
            }

            fclose($handle);

            // Insert in chunks
            foreach (array_chunk($queueItems, 500) as $chunk) {
                $this->smsQueueRepository->bulkCreate($chunk);
            }

            return $processedCount > 0;

        } catch (\Exception $e) {
            Log::error("Excel processing failed: " . $e->getMessage());
            return false;
        }
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            return strtolower(trim($header));
        }, $headers);
    }

    private function validateMobileNumber(string $mobile): bool
    {
        $cleaned = preg_replace('/[^0-9]/', '', $mobile);
        return strlen($cleaned) >= 10;
    }

    private function personalizeMessage(string $message, array $data): string
    {
        return preg_replace_callback('/#p([1-9]|10)#/', function ($matches) use ($data) {
            $param = 'p' . $matches[1];
            return $data[$param] ?? '';
        }, $message);
    }
}