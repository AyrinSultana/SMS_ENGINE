<?php

namespace App\Services\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface HistoryServiceInterface
{
    /**
     * Get SMS history with search functionality
     *
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getSmsHistory(?string $search = null, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get SMS history for a specific template
     *
     * @param int $templateId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getHistoryByTemplate(int $templateId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get history statistics
     *
     * @return array
     */
    public function getHistoryStatistics(): array;
}
