<?php

namespace App\Events;

use App\Models\Template;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TemplateStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The template.
     *
     * @var Template
     */
    public $template;

    /**
     * The new status.
     *
     * @var string
     */
    public $status;

    /**
     * Create a new event instance.
     *
     * @param Template $template
     * @param string $status
     * @return void
     */
    public function __construct(Template $template, string $status)
    {
        $this->template = $template;
        $this->status = $status;
    }
}
