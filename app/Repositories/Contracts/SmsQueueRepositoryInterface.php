<?php

namespace App\Repositories\Contracts;

use App\Models\SmsQueue;
use Illuminate\Database\Eloquent\Collection;

interface SmsQueueRepositoryInterface
{
    /**
     * Get SMS queue items by reference ID
     *
     * @param int $refId
     * @return Collection
     */
    public function getByRefId(int $refId): Collection;
    
    /**
     * Count SMS queue items by reference ID
     *
     * @param int $refId
     * @return int
     */
    public function countByRefId(int $refId): int;
    
    /**
     * Create a new SMS queue item
     *
     * @param array $data
     * @return SmsQueue
     */
    public function create(array $data): SmsQueue;
    
    /**
     * Bulk create SMS queue items
     *
     * @param array $records
     * @return bool
     */
    public function bulkCreate(array $records): bool;
    
    /**
     * Update SMS queue items status by reference ID
     *
     * @param int $refId
     * @param string $status
     * @return bool
     */
    public function updateStatusByRefId(int $refId, string $status): bool;
}
