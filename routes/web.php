<?php

use App\Http\Controllers\DatatableController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\VoicemailController;
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
        SlackAlert::message('Hello world!');
});

Route::controller(WebhookController::class)->group(function () {
    Route::get('tasks', 'index');
    Route::post('pipedrive/webhook', 'store'); //deal updated
    Route::post('pipedrive/webhook/person', 'person_created'); //person created

    //Facebook Webhook
    Route::get('facebook/webhook', 'facebook');
    Route::post('facebook/webhook', 'facebook');
});

Route::controller(TwilioController::class)->group(function () {
    Route::get('send_sms', 'send_sms');
    Route::post('sms_status', 'sms_status');
});

Route::controller(VoicemailController::class)->group(function () {
    Route::get('send_sms', 'send_vm');
    Route::post('vm_status', 'vm_status');
});

Route::get('api/v1/tasks', [DatatableController::class, 'tasks'])->name('tasks.ajax');
