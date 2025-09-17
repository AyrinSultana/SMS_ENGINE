<?php

namespace App\Repositories;

use App\Models\TemplateMessage;
use App\Repositories\Contracts\TemplateMessageRepositoryInterface;
use Illuminate\Support\Collection;

class TemplateMessageRepository implements TemplateMessageRepositoryInterface
{
    /**
     * @var TemplateMessage
     */
    protected $model;

    /**
     * TemplateMessageRepository constructor.
     *
     * @param TemplateMessage $model
     */
    public function __construct(TemplateMessage $model)
    {
        $this->model = $model;
    }

    /**
     * Get all template messages
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->with('template')->get();
    }

    /**
     * Get template message by ID
     *
     * @param int $id
     * @return TemplateMessage|null
     */
    public function find(int $id): ?TemplateMessage
    {
        return $this->model->with('template')->find($id);
    }

    /**
     * Get messages by template ID
     *
     * @param int $templateId
     * @return Collection
     */
    public function findByTemplateId(int $templateId): Collection
    {
        return $this->model->where('template_id', $templateId)
            ->with('template')
            ->get();
    }

    /**
     * Create a new template message
     *
     * @param array $data
     * @return TemplateMessage
     */
    public function create(array $data): TemplateMessage
    {
        return $this->model->create($data);
    }

    /**
     * Update a template message
     *
     * @param int $id
     * @param array $data
     * @return TemplateMessage|null
     */
    public function update(int $id, array $data): ?TemplateMessage
    {
        $templateMessage = $this->find($id);

        if (!$templateMessage) {
            return null;
        }

        $templateMessage->update($data);

        return $templateMessage;
    }

    /**
     * Delete a template message
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $templateMessage = $this->find($id);

        if (!$templateMessage) {
            return false;
        }

        return $templateMessage->delete();
    }

    /**
     * Delete all messages for a template
     *
     * @param int $templateId
     * @return bool
     */
    public function deleteByTemplateId(int $templateId): bool
    {
        return $this->model->where('template_id', $templateId)->delete();
    }
}
