<?php

namespace App\Services\Contracts;

use App\Models\Template;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface TemplateServiceInterface
{
    /**
     * Get all approved templates grouped by name
     *
     * @return Collection
     */
    public function getApprovedTemplates(): Collection;

    /**
     * Get all distinct template names
     * 
     * @param string|null $approvalStatus
     * @return Collection
     */
    public function getDistinctTemplateNames(?string $approvalStatus = null): Collection;

    /**
     * Get template by ID
     *
     * @param int $id
     * @return Template|null
     */
    public function getTemplateById(int $id): ?Template;

    /**
     * Get templates by name
     *
     * @param string $name
     * @return Collection
     */
    public function getTemplatesByName(string $name): Collection;

    /**
     * Create a new template
     *
     * @param array $data
     * @param UploadedFile|null $file
     * @return Template
     */
    public function createTemplate(array $data, ?UploadedFile $file = null): Template;

    /**
     * Update a template
     *
     * @param int $id
     * @param array $data
     * @return Template|null
     */
    public function updateTemplate(int $id, array $data): ?Template;

    /**
     * Delete a template
     *
     * @param int $id
     * @return bool
     */
    public function deleteTemplate(int $id): bool;

    /**
     * Update template approval status
     *
     * @param string $name
     * @param string $status
     * @return bool
     */
    public function updateTemplateStatus(string $name, string $status): bool;

    /**
     * Get template statistics for admin dashboard
     *
     * @return Collection
     */
    public function getTemplateStatistics(): Collection;

    /**
     * Generate CSV data for a template
     *
     * @param string $templateName
     * @return array|null
     */
    public function generateTemplateCSV(string $templateName): ?array;

    /**
     * Get all templates
     *
     * @return Collection
     */
    public function getAllTemplates(): Collection;


    public function getTemplateByName(string $name);
}
