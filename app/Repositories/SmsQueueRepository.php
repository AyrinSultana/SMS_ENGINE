<?php

namespace App\Repositories;

use App\Models\SmsQueue;
use App\Repositories\Contracts\SmsQueueRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SmsQueueRepository implements SmsQueueRepositoryInterface
{
    /**
     * @var SmsQueue
     */
    protected $model;
    
    /**
     * SmsQueueRepository constructor.
     *
     * @param SmsQueue $model
     */
    public function __construct(SmsQueue $model)
    {
        $this->model = $model;
    }
    
    /**
     * Get SMS queue items by reference ID
     *
     * @param int $ExcelId
     * 
     * @return Collection
     */
    public function getByRefId(int $ExcelId): Collection
    {
        return $this->model->where('excel_id', $ExcelId)->get();
    }
    
    /**
     * Count SMS queue items by reference ID
     *
     * @param int $ExcelId
     * @return int
     */
    public function countByRefId(int $ExcelId): int
    {
        return $this->model->where('excel_id', $ExcelId)->count();
    }
    
    /**
     * Create a new SMS queue item
     *
     * @param array $data
     * @return SmsQueue
     */
//    public function create(array $data): ?SmsHistory
// {
//     try {
//         // Add validation for required fields
//         $required = ['mobile_no', 'template_name', 'message', 'status'];
//         foreach ($required as $field) {
//             if (empty($data[$field])) {
//                 throw new \Exception("Missing required field: $field");
//             }
//         }
        
//         return $this->model->create($data);
//     } catch (\Exception $e) {
//         Log::error("History create failed", [
//             'error' => $e->getMessage(),
//             'data' => $data
//         ]);
//         return null;
//     }


// }

public function create(array $data): SmsQueue
{
    try {
        // Validate required fields matching your DB columns
        $required = ['mobile', 'msg', 'status'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Missing required field: $field");
            }
        }

        return $this->model->create($data);
    } catch (\Exception $e) {
        Log::error("SmsQueue create failed", [
            'error' => $e->getMessage(),
            'data' => $data
        ]);
        throw $e;
    }
}


      
    /**
     * Bulk create SMS queue items
     *
     * @param array $records
     * @return bool
     */
    public function bulkCreate(array $records): bool
{
    try {
        if (empty($records)) return false;
        
        // Ensure all records have required fields
        foreach ($records as $record) {
            if (empty($record['mobile']) || empty($record['msg'])) {
                throw new \Exception("Invalid queue record format");
            }
        }
        
        return $this->model->insert($records);
    } catch (\Exception $e) {
        Log::error("Queue bulk create failed", [
            'error' => $e->getMessage(),
            'first_record' => $records[0] ?? null
        ]);
        return false;
    }
}
    
    /**
     * Update SMS queue items status by reference ID
     *
     * @param int $ExcelId
     * @param string $status
     * @return bool
     */
    public function updateStatusByRefId(int $ExcelId, string $status): bool
    {
        // We need to pass the status as a raw value to avoid quoting issues with enums
        return $this->model->where('excel_id', $ExcelId)
            ->update(['status' => $status]);
    }
}
