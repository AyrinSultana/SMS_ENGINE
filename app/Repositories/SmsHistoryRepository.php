<?php

namespace App\Repositories;

use App\Models\SmsHistory;
use App\Repositories\Contracts\SmsHistoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SmsHistoryRepository implements SmsHistoryRepositoryInterface
{
    /**
     * @var SmsHistory
     */
    protected $model;

    /**
     * SmsHistoryRepository constructor.
     *
     * @param SmsHistory $model
     */
    public function __construct(SmsHistory $model)
    {
        $this->model = $model;
    }

    /**
     * Get SMS history with search functionality
     *
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getHistory(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('template');

        // Apply search if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('template_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('message', 'LIKE', '%' . $search . '%')
                    ->orWhere('recipient', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->orWhere('source', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('template', function ($templateQuery) use ($search) {
                        $templateQuery->where('name', 'LIKE', '%' . $search . '%');
                    })
                    // Add timestamp search support
                    ->orWhere(function ($dateQuery) use ($search) {
                        // Search in formatted date string (dd-mm-yyyy format)
                        $dateQuery->whereRaw("DATE_FORMAT(modified_at, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                            // Search in formatted datetime string (dd-mm-yyyy HH:mm:ss format)  
                            ->orWhereRaw("DATE_FORMAT(modified_at, '%d-%m-%Y %H:%i:%s') LIKE ?", ['%' . $search . '%'])
                            // Search in raw timestamp format (yyyy-mm-dd)
                            ->orWhereRaw("DATE(modified_at) LIKE ?", ['%' . $search . '%']);
                    });
            });
        }

        // Ordering and pagination
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get SMS history for a specific template
     *
     * @param int $templateId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByTemplateId(int $templateId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('template')
            ->where('template_id', $templateId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new SMS history record
     *
     * @param array $data
     * @return SmsHistory
     */
    public function create(array $data): SmsHistory
    {
        try {
        return $this->model->create($data);
    } catch (\Exception $e) {
        \Log::error("Failed to create SMS history: " . $e->getMessage(), [
            'data' => $data,
            'exception' => $e
        ]);
        return null;
    }
    }

    /**
     * Get history statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $totalCount = $this->model->count();
        $sentCount = $this->model->where('status', 'sent')->count();
        $pendingCount = $this->model->where('status', 'pending')->count();
        $failedCount = $this->model->where('status', 'failed')->count();

        return [
            'total' => $totalCount,
            'sent' => $sentCount,
            'pending' => $pendingCount,
            'failed' => $failedCount,
        ];
    }

    /**
     * Update status of SMS history records
     *
     * @param array $criteria Criteria to find records (template_id, recipient, mobile_no, etc.)
     * @param string $status New status value
     * @return bool
     */
    public function updateStatus(array $criteria, string $status): bool
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $key => $value) {
            if (is_array($value) && count($value) === 2 && $value[0] === 'LIKE') {
                $query->where($key, 'LIKE', $value[1]);
            } elseif ($key === 'created_at') {
                // For created_at timestamp, use approximate matching (within the same minute)
                $dt = new \DateTime($value);
                $startTime = $dt->format('Y-m-d H:i:00');
                $endTime = $dt->format('Y-m-d H:i:59');
                $query->whereBetween('created_at', [$startTime, $endTime]);
            } else {
                $query->where($key, $value);
            }
        }

        // Don't modify the source field when updating status
        return $query->update(['status' => $status, 'modified_at' => now()]);
    }

    /**
     * Update SMS history records status by reference ID
     *
     * @param int $refId
     * @param string $status
     * @return bool
     */
    public function updateStatusByRefId(int $refId, string $status): bool
    {
        // Find SMS history records that match the reference ID by finding
        // records created around the same time as the pending record
        $pendingRecord = \App\Models\PendingList::find($refId);

        if (!$pendingRecord) {
            return false;
        }

        $query = $this->model->newQuery();

        // For file uploads, match by template and approximate timestamp
        if (isset($pendingRecord->file_path) && $pendingRecord->file_path) {
            $template = $pendingRecord->template;

            if ($template) {
                $createdTime = $pendingRecord->created_at ?? $pendingRecord->timestamp;
                $dt = new \DateTime($createdTime);
                $startTime = $dt->format('Y-m-d H:i:00');
                $endTime = $dt->format('Y-m-d H:i:59');

                $query->where('template_id', $template->id)
                    ->whereBetween('created_at', [$startTime, $endTime]);
            }
        } else {
            // For direct entries, match by template and approximate timestamp
            $template = $pendingRecord->template;

            if ($template) {
                $createdTime = $pendingRecord->created_at ?? $pendingRecord->timestamp;
                $dt = new \DateTime($createdTime);
                $startTime = $dt->format('Y-m-d H:i:00');
                $endTime = $dt->format('Y-m-d H:i:59');

                $query->where('template_id', $template->id)
                    ->whereBetween('created_at', [$startTime, $endTime]);
            }
        }

        // Use the query we built instead of looking for a non-existent 'refid' column
        return $query->update(['status' => $status, 'modified_at' => now()]);
    }


public function getGroupedHistory(?string $search = null, int $perPage = 15): array
{
    // First get the grouped/paginated summary
    $groupQuery = $this->model->newQuery();

    if (!empty($search)) {
        $groupQuery->where(function ($q) use ($search) {
            $q->where('template_name', 'LIKE', "%{$search}%")
              ->orWhere('message', 'LIKE', "%{$search}%")
              ->orWhere('recipient', 'LIKE', "%{$search}%")
              ->orWhere('mobile_no', 'like', "%{$search}%");
        });
    }

    $groupedResults = $groupQuery
        ->select(
            'template_id',
            'template_name',
            \DB::raw('COUNT(*) as total_messages'),
            \DB::raw('MAX(modified_at) as last_modified')
        )
        ->groupBy('template_id', 'template_name')
        ->orderByDesc('last_modified')
        ->paginate($perPage);

    // Then get all individual messages for these templates
    $templateIds = $groupedResults->pluck('template_id')->toArray();
    
    $messagesQuery = $this->model->whereIn('template_id', $templateIds);
    
    if (!empty($search)) {
        $messagesQuery->where(function ($q) use ($search) {
            $q->where('template_name', 'LIKE', "%{$search}%")
              ->orWhere('message', 'LIKE', "%{$search}%")
              ->orWhere('recipient', 'LIKE', "%{$search}%")
              ->orWhere('mobile_no', 'like', "%{$search}%");
        });
    }

    $messages = $messagesQuery
        ->orderByDesc('modified_at')
        ->get()
        ->groupBy('template_id');

    return [
        'grouped' => $groupedResults,
        'messages' => $messages
    ];
}





    

}
