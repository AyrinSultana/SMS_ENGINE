<?php

namespace App\Repositories;

use App\Models\PendingList;
use App\Repositories\Contracts\PendingListRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PendingListRepository implements PendingListRepositoryInterface
{
    /**
     * @var PendingList
     */
    protected $model;

    /**
     * PendingListRepository constructor.
     *
     * @param PendingList $model
     */
    public function __construct(PendingList $model)
    {
        $this->model = $model;
    }

    /**
     * Get all pending SMS records
     *
     * @param string $status
     * @return Collection
     */
    public function getByStatus(string $status = 'pending'): Collection
    {
        return $this->model->with('template')
            ->where('status', $status)
            ->orderBy('timestamp', 'desc')
            ->get();
    }

    /**
     * Find a pending list record by ID
     *
     * @param int $id
     * @return PendingList|null
     */
    public function find(int $id): ?PendingList
    {
        return $this->model->with('template')->find($id);
    }

    /**
     * Create a new pending list record
     *
     * @param array $data
     * @return PendingList
     */
    public function create(array $data): PendingList
    {
        // Ensure status is set to 'pending' if not explicitly provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->model->create($data);
    }

    /**
     * Update a pending list record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $pendingList = $this->find($id);

        if (!$pendingList) {
            return false;
        }

        return $pendingList->update($data);
    }

    /**
     * Get paginated pending SMS records
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedByStatus(string $status = 'pending', int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with('template')
            ->where('status', $status)
            ->orderBy('timestamp', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all paginated SMS records regardless of status
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with('template')
            ->orderBy('timestamp', 'desc')
            ->paginate($perPage);
    }
}
