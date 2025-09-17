<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\SmsHistoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// SMS API Routes
Route::post('/sms/queue', [SmsController::class, 'queueSms']);
Route::get('/sms/history/stats', [SmsHistoryController::class, 'getStatistics']);

// Template API Routes
Route::get('/templates', [TemplateController::class, 'apiIndex']);
Route::get('/templates/{id}', [TemplateController::class, 'apiShow']);
Route::post('/templates', [TemplateController::class, 'apiStore']);
Route::put('/templates/{id}', [TemplateController::class, 'apiUpdate']);
Route::delete('/templates/{id}', [TemplateController::class, 'apiDestroy']);
