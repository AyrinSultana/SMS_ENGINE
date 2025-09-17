<?php

namespace App\Repositories\Contracts;

use App\Models\TemplateMessage;
use Illuminate\Support\Collection;

interface TemplateMessageRepositoryInterface
{
    /**
     * Get all template messages
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Get template message by ID
     *
     * @param int $id
     * @return TemplateMessage|null
     */
    public function find(int $id): ?TemplateMessage;

    /**
     * Get messages by template ID
     *
     * @param int $templateId
     * @return Collection
     */
    public function findByTemplateId(int $templateId): Collection;

    /**
     * Create a new template message
     *
     * @param array $data
     * @return TemplateMessage
     */
    public function create(array $data): TemplateMessage;

    /**
     * Update a template message
     *
     * @param int $id
     * @param array $data
     * @return TemplateMessage|null
     */
    public function update(int $id, array $data): ?TemplateMessage;

    /**
     * Delete a template message
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Delete all messages for a template
     *
     * @param int $templateId
     * @return bool
     */
    public function deleteByTemplateId(int $templateId): bool;
}
