<?php

namespace App\Repositories\Contracts;

use App\Models\PendingList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PendingListRepositoryInterface
{
    /**
     * Get all pending SMS records
     *
     * @param string $status
     * @return Collection
     */
    public function getByStatus(string $status = 'pending'): Collection;
    
    /**
     * Find a pending list record by ID
     *
     * @param int $id
     * @return PendingList|null
     */
    public function find(int $id): ?PendingList;
    
    /**
     * Create a new pending list record
     *
     * @param array $data
     * @return PendingList
     */
    public function create(array $data): PendingList;
    
    /**
     * Update a pending list record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;
    
    /**
     * Get paginated pending SMS records
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedByStatus(string $status = 'pending', int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Get all paginated SMS records regardless of status
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedAll(int $perPage = 10): LengthAwarePaginator;
}
