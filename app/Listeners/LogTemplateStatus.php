<?php

namespace App\Listeners;

use App\Events\TemplateStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogTemplateStatus implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  TemplateStatusChanged  $event
     * @return void
     */
    public function handle(TemplateStatusChanged $event)
    {
        Log::info('Template status changed', [
            'template_id' => $event->template->id,
            'template_name' => $event->template->name,
            'old_status' => $event->template->getOriginal('approval_status'),
            'new_status' => $event->status,
        ]);
    }
}
