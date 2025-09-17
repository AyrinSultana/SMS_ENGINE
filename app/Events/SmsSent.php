<?php

namespace App\Events;

use App\Models\PendingList;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmsSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The pending list record.
     *
     * @var PendingList
     */
    public $pendingRecord;

    /**
     * Create a new event instance.
     *
     * @param PendingList $pendingRecord
     * @return void
     */
    public function __construct(PendingList $pendingRecord)
    {
        $this->pendingRecord = $pendingRecord;
    }
}
