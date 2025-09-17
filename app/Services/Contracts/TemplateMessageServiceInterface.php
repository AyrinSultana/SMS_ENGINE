<?php

namespace App\Services\Contracts;

use App\Models\TemplateMessage;
use Illuminate\Support\Collection;

interface TemplateMessageServiceInterface
{
    /**
     * Get all template messages
     *
     * @return Collection
     */
    public function getAllTemplateMessages(): Collection;

    /**
     * Get template message by ID
     *
     * @param int $id
     * @return TemplateMessage|null
     */
    public function getTemplateMessageById(int $id): ?TemplateMessage;

    /**
     * Get messages by template ID
     *
     * @param int $templateId
     * @return Collection
     */
    public function getMessagesByTemplateId(int $templateId): Collection;

    /**
     * Create a new template message
     *
     * @param array $data
     * @return TemplateMessage
     */
    public function createTemplateMessage(array $data): TemplateMessage;

    /**
     * Update a template message
     *
     * @param int $id
     * @param array $data
     * @return TemplateMessage|null
     */
    public function updateTemplateMessage(int $id, array $data): ?TemplateMessage;

    /**
     * Delete a template message
     *
     * @param int $id
     * @return bool
     */
    public function deleteTemplateMessage(int $id): bool;

    /**
     * Create multiple messages for a template
     *
     * @param int $templateId
     * @param array $messages Array of messages with title and message
     * @return Collection
     */
    public function createMultipleMessages(int $templateId, array $messages): Collection;

    /**
     * Delete all messages for a template
     *
     * @param int $templateId
     * @return bool
     */
    public function deleteMessagesByTemplateId(int $templateId): bool;
}
