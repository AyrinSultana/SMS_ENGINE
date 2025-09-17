<?php

namespace App\Providers;

use App\Events\SmsSent;
use App\Events\TemplateStatusChanged;
use App\Listeners\LogSmsStatus;
use App\Listeners\LogTemplateStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // SMS events
        SmsSent::class => [
            LogSmsStatus::class,
        ],
        
        // Template events
        TemplateStatusChanged::class => [
            LogTemplateStatus::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
