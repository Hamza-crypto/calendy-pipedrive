<?php

use App\Http\Controllers\DatatableController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Spatie\SlackAlerts\Facades\SlackAlert;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');


});

Route::get('/test2', function () {
    dd('This is Hamza');

});

Route::get('/test', function () {
    SlackAlert::message('This is a test message from Hamza');

});

Route::controller(WebhookController::class)->group(function () {
    Route::get('tasks', 'index');
    Route::post('pipedrive/webhook', 'store');

    //Facebook Webhook
    Route::get('facebook/webhook', 'facebook');
    Route::post('facebook/webhook', 'facebook');
});

Route::controller(TwilioController::class)->group(function () {
    Route::get('send_sms', 'send_sms');
    Route::post('sms_status', 'sms_status');

});

Route::get('api/v1/tasks', [DatatableController::class, 'tasks'])->name('tasks.ajax');
