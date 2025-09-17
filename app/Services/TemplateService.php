<?php

namespace App\Services;

use App\Models\Template;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use App\Services\Contracts\TemplateServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class TemplateService implements TemplateServiceInterface
{
    /**
     * @var TemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * TemplateService constructor.
     *
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * Get all approved templates grouped by name
     *
     * @return Collection
     */
    public function getApprovedTemplates(): Collection
    {
        return $this->templateRepository->getApproved();
    }

    /**
     * Get all distinct template names
     * 
     * @param string|null $approvalStatus
     * @return Collection
     */
    public function getDistinctTemplateNames(?string $approvalStatus = null): Collection
    {
        return $this->templateRepository->getDistinctNames($approvalStatus);
    }

    /**
     * Get template by ID
     *
     * @param int $id
     * @return Template|null
     */
    public function getTemplateById(int $id): ?Template
    {
        return $this->templateRepository->find($id);
    }

    /**
     * Get templates by name
     *
     * @param string $name
     * @return Collection
     */
    public function getTemplatesByName(string $name): Collection
    {
        return $this->templateRepository->findByName($name);
    }

    /**
     * Create a new template
     *
     * @param array $data
     * @param UploadedFile|null $file
     * @return Template
     */
    // public function createTemplate(array $data, ?UploadedFile $file = null): Template
    // {
    //     return $this->templateRepository->create($data, $file);
    // }

  public function createTemplate(array $data, ?UploadedFile $file = null): Template
{
    $template = new Template();
    $template->name = $data['name'];
    $template->authorizer = $data['authorizer'] ?? null;

    if ($file) {
        $path = $file->store('templates', 'public');
        $template->file_path = $path;
    }

    $template->save();

    return $template;
}


public function getTemplateByName(string $name)
{
    return Template::where('name', $name)->first();
}

    /**
     * Update a template
     *
     * @param int $id
     * @param array $data
     * @return Template|null
     */
    public function updateTemplate(int $id, array $data): ?Template
    {
        return $this->templateRepository->update($id, $data);
    }

    public function getAllActiveTemplates()
{
    return Template::where('status', 'active') // Only active
                  ->whereNull('deleted_at')    // Not soft-deleted
                  ->get();
}


    /**
     * Delete a template
     *
     * @param int $id
     * @return bool
     */
    public function deleteTemplate(int $id): bool
    {
        return $this->templateRepository->delete($id);
    }

    /**
     * Update template approval status
     *
     * @param string $name
     * @param string $status
     * @return bool
     */
    public function updateTemplateStatus(string $name, string $status): bool
    {
        return $this->templateRepository->updateStatus($name, $status);
    }

    /**
     * Get template statistics for admin dashboard
     *
     * @return Collection
     */
    public function getTemplateStatistics(): Collection
    {
        return $this->templateRepository->getStatistics();
    }

    /**
     * Generate CSV data for a template
     *
     * @param string $templateName
     * @return array|null
     */
    public function generateTemplateCSV(string $templateName): ?array
    {
        $template = $this->templateRepository->findByName($templateName)->first();

        if (!$template) {
            return null;
        }

        // If there's a file path, return that
        if ($template->file_path) {
            $filePath = storage_path('app/public/' . $template->file_path);

            if (file_exists($filePath)) {
                return [
                    'path' => $filePath,
                    'name' => $templateName . '_template.csv',
                    'type' => 'file'
                ];
            }
        }

        // Otherwise generate CSV data
        $templates = $this->templateRepository->findByName($templateName);
        $csvData = "name,message\n";

        foreach ($templates as $template) {
            $csvData .= '"' . $template->name . '","' . $template->temptmsg . "\"\n";
        }

        return [
            'data' => $csvData,
            'name' => $templateName . '_template.csv',
            'type' => 'data'
        ];
    }

    /**
     * Get all templates
     *
     * @return Collection
     */
    public function getAllTemplates(): Collection
    {
        return $this->templateRepository->all();
    }
}
