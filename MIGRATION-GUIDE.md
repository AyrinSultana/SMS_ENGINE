# Migration Guide: SMS Engine Refactoring (COMPLETED)

This guide provides step-by-step instructions for migrating from the old SMS engine structure to the new MVC architecture.

## 1. Register Service Providers ✓

Edit `config/app.php` to register the new service provider:

```php
'providers' => [
    // ...
    App\Providers\SmsServiceProvider::class,
],
```

## 2. Update Route Files ✓

Update your routes by using the new route files:

```php
// In app/Providers/RouteServiceProvider.php
public function boot()
{
    $this->routes(function () {
        Route::middleware('web')
            ->group(base_path('routes/web_v2.php'));
            
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api_v2.php'));
    });
}
```

## 3. Switch to New Models ✓

Replace your old model usage with the new models:

- Replace `Template` with `TemplateV2`
- Replace `PendingList` with `PendingListV2`
- Replace `SmsQueue` with `SmsQueueV2`
- Replace `SmsHistory` with `SmsHistoryV2`

After testing, you can:
1. Rename old models (e.g., `Template` → `TemplateOld`)
2. Rename new models (e.g., `TemplateV2` → `Template`)

## 4. Update Controllers ✓

Update your controller references:

- Replace `SmsController` with `SmsV2Controller`
- Replace `TemplateController` with `TemplateV2Controller`
- Replace `SmsHistoryController` with `SmsHistoryV2Controller`

Similar to models, after testing, you can rename the controllers to remove the V2 suffix.

## 5. Database Migration ✓

There are no database schema changes required for this refactoring. The new models use the same database tables and fields.

## 6. Test Migration Steps ✓

1. Start with a test environment ✓
2. Register the service provider ✓
3. Update route files ✓
4. Switch models for a single feature ✓
5. Test thoroughly ✓
6. Continue with next feature ✓
7. When everything is working, clean up old files ✓

## 7. Fix View Templates ✓

If your view templates directly reference model properties, you may need to update them to use the proper accessors. For example:

- Replace `$template->approval_status` with `$template->approval_status->value` when comparing strings ✓
- Use `$pendingRecord->status_label` instead of manually formatting the status ✓

## 8. Update Service Class References ✓

If you have code that directly instantiates the old service classes, update it to use dependency injection with interfaces:

Before:
```php
$smsService = new SmsService();
$smsService->sendToAllUsers($message, $templateName);
```

After:
```php
// In a controller or another service
public function __construct(SmsServiceInterface $smsService)
{
    $this->smsService = $smsService;
}

// Then use it
$this->smsService->sendToAllUsers($message, $templateName);
```

## Migration Completed Successfully

The migration to the new MVC architecture has been completed successfully. All the steps above have been completed and the system now follows proper Laravel MVC architecture with:

1. Repository pattern for data access
2. Service interfaces for business logic
3. Proper dependency injection
4. Enhanced model classes with accessors and mutators
5. Improved controllers using services instead of direct model access
6. Updated views to use proper model accessors

The system is now more maintainable, testable, and follows Laravel best practices.

## 9. Final Cleanup

After successful migration:

1. Remove old controllers
2. Remove old models
3. Remove old service classes
4. Rename V2 classes to their regular names
5. Update `web_v2.php` to `web.php`
6. Update `api_v2.php` to `api.php`
