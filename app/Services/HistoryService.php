<?php

namespace App\Services;

use App\Repositories\Contracts\SmsHistoryRepositoryInterface;
use App\Services\Contracts\HistoryServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryService implements HistoryServiceInterface
{
    /**
     * @var SmsHistoryRepositoryInterface
     */
    protected $smsHistoryRepository;

    /**
     * HistoryService constructor.
     *
     * @param SmsHistoryRepositoryInterface $smsHistoryRepository
     */
    public function __construct(SmsHistoryRepositoryInterface $smsHistoryRepository)
    {
        $this->smsHistoryRepository = $smsHistoryRepository;
    }

    /**
     * Get SMS history with search functionality
     *
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getSmsHistory(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->smsHistoryRepository->getHistory($search, $perPage);
    }

    /**
     * Get SMS history for a specific template
     *
     * @param int $templateId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getHistoryByTemplate(int $templateId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->smsHistoryRepository->getByTemplateId($templateId, $perPage);
    }

    /**
     * Get history statistics
     *
     * @return array
     */
    public function getHistoryStatistics(): array
    {
        return $this->smsHistoryRepository->getStatistics();
    }

    
}
