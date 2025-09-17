<?php

namespace App\Providers;

use App\Repositories\Contracts\PendingListRepositoryInterface;
use App\Repositories\Contracts\SmsHistoryRepositoryInterface;
use App\Repositories\Contracts\SmsQueueRepositoryInterface;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use App\Repositories\Contracts\TemplateMessageRepositoryInterface;
use App\Repositories\PendingListRepository;
use App\Repositories\SmsHistoryRepository;
use App\Repositories\SmsQueueRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\TemplateMessageRepository;
use App\Services\Contracts\HistoryServiceInterface;
use App\Services\Contracts\SmsServiceInterface;
use App\Services\Contracts\TemplateServiceInterface;
use App\Services\Contracts\TemplateMessageServiceInterface;
use App\Services\HistoryService;
use App\Services\SmsService;
use App\Services\TemplateService;
use App\Services\TemplateMessageService;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind repositories
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
        $this->app->bind(TemplateMessageRepositoryInterface::class, TemplateMessageRepository::class);
        $this->app->bind(SmsHistoryRepositoryInterface::class, SmsHistoryRepository::class);
        $this->app->bind(PendingListRepositoryInterface::class, PendingListRepository::class);
        $this->app->bind(SmsQueueRepositoryInterface::class, SmsQueueRepository::class);

        // Bind services
        $this->app->bind(TemplateServiceInterface::class, TemplateService::class);
        $this->app->bind(TemplateMessageServiceInterface::class, TemplateMessageService::class);
        $this->app->bind(SmsServiceInterface::class, SmsService::class);
        $this->app->bind(HistoryServiceInterface::class, HistoryService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
