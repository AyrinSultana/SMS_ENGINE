<?php

namespace App\Services;

use App\Models\TemplateMessage;
use App\Repositories\Contracts\TemplateMessageRepositoryInterface;
use App\Services\Contracts\TemplateMessageServiceInterface;
use Illuminate\Support\Collection;

class TemplateMessageService implements TemplateMessageServiceInterface
{
    /**
     * @var TemplateMessageRepositoryInterface
     */
    protected $templateMessageRepository;

    /**
     * TemplateMessageService constructor.
     *
     * @param TemplateMessageRepositoryInterface $templateMessageRepository
     */
    public function __construct(TemplateMessageRepositoryInterface $templateMessageRepository)
    {
        $this->templateMessageRepository = $templateMessageRepository;
    }

    /**
     * Get all template messages
     *
     * @return Collection
     */
    public function getAllTemplateMessages(): Collection
    {
        return $this->templateMessageRepository->all();
    }

    /**
     * Get template message by ID
     *
     * @param int $id
     * @return TemplateMessage|null
     */
    public function getTemplateMessageById(int $id): ?TemplateMessage
    {
        return $this->templateMessageRepository->find($id);
    }

    /**
     * Get messages by template ID
     *
     * @param int $templateId
     * @return Collection
     */
    public function getMessagesByTemplateId(int $templateId): Collection
    {
        return $this->templateMessageRepository->findByTemplateId($templateId);
    }

    /**
     * Create a new template message
     *
     * @param array $data
     * @return TemplateMessage
     */
    public function createTemplateMessage(array $data): TemplateMessage
    {
        return $this->templateMessageRepository->create($data);
    }

    /**
     * Update a template message
     *
     * @param int $id
     * @param array $data
     * @return TemplateMessage|null
     */
    public function updateTemplateMessage(int $id, array $data): ?TemplateMessage
    {
        return $this->templateMessageRepository->update($id, $data);
    }

    /**
     * Delete a template message
     *
     * @param int $id
     * @return bool
     */
    public function deleteTemplateMessage(int $id): bool
    {
        return $this->templateMessageRepository->delete($id);
    }

    /**
     * Create multiple messages for a template
     *
     * @param int $templateId
     * @param array $messages Array of messages with title and message
     * @return Collection
     */
    public function createMultipleMessages(int $templateId, array $messages): Collection
    {
        $createdMessages = collect();

        foreach ($messages as $messageData) {
            if (isset($messageData['title']) && isset($messageData['message'])) {
                $data = [
                    'template_id' => $templateId,
                    'title' => $messageData['title'],
                    'message' => $messageData['message']
                ];

                $createdMessages->push($this->createTemplateMessage($data));
            }
        }

        return $createdMessages;
    }

    /**
     * Delete all messages for a template
     *
     * @param int $templateId
     * @return bool
     */
    public function deleteMessagesByTemplateId(int $templateId): bool
    {
        return $this->templateMessageRepository->deleteByTemplateId($templateId);
    }
}
