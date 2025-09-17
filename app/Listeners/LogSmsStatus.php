<?php

namespace App\Listeners;

use App\Events\SmsSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSmsStatus implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  SmsSent  $event
     * @return void
     */
    public function handle(SmsSent $event)
    {
        Log::info('SMS sent', [
            'pending_id' => $event->pendingRecord->id,
            'template_name' => $event->pendingRecord->template_name, // Uses accessor method
            'status' => $event->pendingRecord->status,
            'timestamp' => $event->pendingRecord->timestamp,
        ]);
    }
}
