<?php

namespace App\Repositories\Contracts;

use App\Models\SmsHistory;
use Illuminate\Pagination\LengthAwarePaginator;

interface SmsHistoryRepositoryInterface
{
    /**
     * Get SMS history with search functionality
     *
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getHistory(?string $search = null, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get SMS history for a specific template
     *
     * @param int $templateId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByTemplateId(int $templateId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a new SMS history record
     *
     * @param array $data
     * @return SmsHistory
     */
    public function create(array $data): SmsHistory;

    /**
     * Get history statistics
     *
     * @return array
     */
    public function getStatistics(): array;

    /**
     * Update status of SMS history records
     *
     * @param array $criteria Criteria to find records (template_id, recipient, mobile_no, etc.)
     * @param string $status New status value
     * @return bool
     */
    public function updateStatus(array $criteria, string $status): bool;

    /**
     * Update SMS history records status by reference ID
     *
     * @param int $refId
     * @param string $status
     * @return bool
     */
    public function updateStatusByRefId(int $refId, string $status): bool;



    /**
 * Get grouped SMS history by template name
 *
 * @param int $perPage
 * @return LengthAwarePaginator
 */
public function getGroupedHistory(?string $search = null, int $perPage = 15): array;


}
