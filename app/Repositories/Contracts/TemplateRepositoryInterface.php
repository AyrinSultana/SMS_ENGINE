<?php

namespace App\Repositories\Contracts;

use App\Models\Template;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

interface TemplateRepositoryInterface
{
    /**
     * Get all templates
     *
     * @return Collection
     */
    public function all(): Collection;
    
    /**
     * Get template by ID
     *
     * @param int $id
     * @return Template|null
     */
    public function find(int $id): ?Template;
    
    /**
     * Get templates by name
     *
     * @param string $name
     * @return Collection
     */
    public function findByName(string $name): Collection;
    
    /**
     * Get all approved templates
     *
     * @return Collection
     */
    public function getApproved(): Collection;
    
    /**
     * Get all distinct template names
     *
     * @param string|null $approvalStatus
     * @return Collection
     */
    public function getDistinctNames(?string $approvalStatus = null): Collection;
    
    /**
     * Create a new template
     *
     * @param array $data
     * @param UploadedFile|null $file
     * @return Template
     */
    public function create(array $data, ?UploadedFile $file = null): Template;
    
    /**
     * Update a template
     *
     * @param int $id
     * @param array $data
     * @return Template|null
     */
    public function update(int $id, array $data): ?Template;
    
    /**
     * Delete a template
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
    
    /**
     * Update template approval status
     *
     * @param string $name
     * @param string $status
     * @return bool
     */
    public function updateStatus(string $name, string $status): bool;
    
    /**
     * Get template statistics
     *
     * @return Collection
     */
    public function getStatistics(): Collection;
}
