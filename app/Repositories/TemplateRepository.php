<?php

namespace App\Repositories;

use App\Models\Template;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TemplateRepository implements TemplateRepositoryInterface
{
    /**
     * @var Template
     */
    protected $model;

    /**
     * TemplateRepository constructor.
     *
     * @param Template $model
     */
    public function __construct(Template $model)
    {
        $this->model = $model;
    }

    /**
     * Get all templates
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->with('templateMessages')->get();
    }

    /**
     * Get template by ID
     *
     * @param int $id
     * @return Template|null
     */
    public function find(int $id): ?Template
    {
        return $this->model->with('templateMessages')->find($id);
    }

    /**
     * Get templates by name
     *
     * @param string $name
     * @return Collection
     */
    public function findByName(string $name): Collection
    {
        return $this->model->where('name', $name)->get();
    }

    /**
     * Get all approved templates
     *
     * @return Collection
     */
    public function getApproved(): Collection
    {
        return $this->model->where('approval_status', 'approved')
            ->get()
            ->groupBy('name');
    }

    /**
     * Get all distinct template names
     *
     * @param string|null $approvalStatus
     * @return Collection
     */
    public function getDistinctNames(?string $approvalStatus = null): Collection
    {
        $query = $this->model->query();

        if ($approvalStatus) {
            $query->where('approval_status', $approvalStatus);
        }

        return $query->distinct()->pluck('name');
    }

    /**
     * Create a new template
     *
     * @param array $data
     * @param UploadedFile|null $file
     * @return Template
     */
    public function create(array $data, ?UploadedFile $file = null): Template
    {
        $template = new Template();
        $template->name = $data['name'];
        $template->approval_status = $data['approval_status'] ?? 'pending';

        if ($file) {
            $originalFileName = $file->getClientOriginalName();
            $storedFilePath = $file->store('templates', 'public');
            $template->file_path = $storedFilePath;
        }

        $template->save();

        return $template;
    }

    /**
     * Update a template
     *
     * @param int $id
     * @param array $data
     * @return Template|null
     */
    public function update(int $id, array $data): ?Template
    {
        $template = $this->find($id);

        if (!$template) {
            return null;
        }

        $template->name = $data['name'] ?? $template->name;

        if (isset($data['approval_status'])) {
            $template->approval_status = $data['approval_status'];
        }

        $template->save();

        return $template;
    }

    /**
     * Delete a template
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $template = $this->find($id);

        if (!$template) {
            return false;
        }

        // Delete associated file if it exists
        // if ($template->file_path) {
        //     Storage::disk('public')->delete($template->file_path);
        // }

        //return $template->delete();
        return $template->update(['status' => 'inactive']);
    }

    /**
     * Update template approval status
     *
     * @param string $name
     * @param string $status
     * @return bool
     */
    public function updateStatus(string $name, string $status): bool
    {
        return $this->model->where('name', $name)
            ->update(['approval_status' => $status]);
    }

    /**
     * Get template statistics
     *
     * @return Collection
     */
    public function getStatistics(): Collection
    {
        return DB::table('template')
            ->select(
                'id',
                'name',
                DB::raw('MAX(approval_status) as approval_status'),
                DB::raw('COUNT(*) as entries_count'),
                DB::raw('MAX(CASE WHEN file_path IS NOT NULL THEN 1 ELSE 0 END) as has_file')
            )
            ->groupBy('id', 'name')
            ->orderBy('name')
            ->get();
    }
}
