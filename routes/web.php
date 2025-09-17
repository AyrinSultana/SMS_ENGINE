<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsHistoryController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes for Controllers
|--------------------------------------------------------------------------
|
| These are the updated web routes using the refactored V2 controllers.
| Use these routes once you have migrated to the new MVC architecture.
|
*/

// Welcome route (unchanged)
Route::get('/', function () {
    return view('welcome');
});

// Template routes
Route::prefix('template')->name('template.')->group(function () {
    Route::get('/', [TemplateController::class, 'index'])->name('index');
    Route::get('/create', [TemplateController::class, 'create'])->name('create');
    Route::post('/', [TemplateController::class, 'store'])->name('store');
    //Route::get('/{id}/edit', [TemplateController::class, 'edit'])->name('edit');
    //Route::get('/template/{id}/edit', [TemplateController::class, 'edit'])->name('template.edit');
    Route::get('/{id}/edit', [TemplateController::class, 'edit'])->name('edit');


    Route::put('/{id}', [TemplateController::class, 'update'])->name('update');
    Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('destroy');
    Route::get('/view/{name}', [TemplateController::class, 'viewMessages'])->name('view');
    
    // Template-related SMS routes
    Route::get('/sms_form', [TemplateController::class, 'showSmsForm'])->name('sms_form');
    Route::get('/fetch-messages', [TemplateController::class, 'fetchTemplateMessages'])->name('fetchTemplateMessages');
    
    // Template authorization routes
    Route::get('/sms_form_auth', [SmsController::class, 'showSmsStatus']);
    Route::get('/download-template/{templateName}', [TemplateController::class, 'downloadTemplateData'])->name('download');
});

// Auth routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/auth_list', [SmsController::class, 'authStatus'])->name('list');
    Route::get('/auth_list_template', [TemplateController::class, 'authListTemplate'])->name('list_template');
});

// Template status update route
Route::post('/update-template-status', [TemplateController::class, 'updateTemplateStatus'])->name('update.template.status');

// SMS routes
Route::post('/send-sms', [SmsController::class, 'sendSms'])->name('send.sms');
Route::post('/update-status', [SmsController::class, 'updateStatus'])->name('updateStatus');
Route::get('/waiting_list', [SmsController::class, 'waiting_list'])->name('waiting_list');
Route::post('/waiting_list', [SmsController::class, 'waiting_list']);

// SMS History routes
Route::get('/sms-history', [SmsHistoryController::class, 'index'])->name('sms.history');
Route::get('/sms-history/template/{templateId}', [SmsHistoryController::class, 'templateHistory'])->name('sms.template.history');

// Login route (unchanged)
Route::get('/login/{id}', [LoginController::class, 'user_access']);

// Laravel Auth Routes
Route::get('/login', function() { return view('auth.login'); })->name('login');
Route::get('/register', function() { return view('auth.register'); })->name('register');
