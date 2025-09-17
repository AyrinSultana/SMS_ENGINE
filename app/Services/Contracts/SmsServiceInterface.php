<?php

namespace App\Services\Contracts;

use App\Models\PendingList;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SmsServiceInterface
{
    /**
     * Send SMS to all users
     *
     * @param string $message
     * @param string $templateName
     * @param string $authorizerName
     * @return bool
     */
    public function sendToAllUsers(string $message, string $templateName, string $authorizerName): bool;

    /**
     * Send SMS to comma-separated list of numbers
     *
     * @param string $numbers
     * @param string $message
     * @param string $templateName
     * @param string $authorizerName
     * @return bool
     */
    public function sendToCommaSeparatedNumbers(string $numbers, string $message, string $templateName, string $authorizerName): bool;

    /**
     * Send SMS from an uploaded Excel/CSV file
     *
     * @param UploadedFile $file
     * @param string $message
     * @param string $templateName
     * @param string $authorizerName
     * @return bool
     */
    public function sendFromExcel(UploadedFile $file, string $message, string $templateName, string $authorizerName): bool;

    /**
     * Process an SMS file to extract numbers and send messages
     *
     * @param PendingList $pendingRecord
     * @param UploadedFile $file
     * @param string $message
     * @param string $authorizerName
     * @return bool
     */
    public function processSmsFile(PendingList $pendingRecord, UploadedFile $file, string $message, string $authorizerName): bool;

    /**
     * Queue an SMS for sending
     *
     * @param string|null $mobile
     * @param string|null $message
     * @param string|null $templateName
     * @param array|null $personalData
     * @return bool
     */
    public function queueSms(?string $mobile = null, ?string $message = null, ?string $templateName = null, ?array $personalData = null): bool;

    /**
     * Log SMS history for a pending record
     *
     * @param PendingList $pendingRecord
     * @param string $status
     * @param string $authorizerName
     * @return void
     */
    public function logSmsHistory(PendingList $pendingRecord, string $status, string $authorizerName): void;

    /**
     * Get all pending SMS records
     *
     * @return Collection
     */
    public function getWaitingList(): Collection;

    /**
     * Get paginated SMS records (pending status only)
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedWaitingList(int $perPage = 10): LengthAwarePaginator;

    /**
     * Get paginated SMS records (all statuses)
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedAllRecords(int $perPage = 10): LengthAwarePaginator;

    /**
     * Update the status of a pending SMS
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateSmsStatus(int $id, string $status,  string $authorizerName): bool;

    /**
     * Personalize message with data
     *
     * @param string $message
     * @param array $data
     * @return string
     */
    public function personalizeMessage(string $message, array $data): string;

    /**
     * Approve SMS records by ID, updating status instead of creating new records
     *
     * @param int $id ID of the pending record or template ID
     * @param string $type Type of approval ('pending_list', 'template', 'all')
     * @return bool
     */
    public function approveSms(int $id, string $type = 'pending_list'): bool;
}
