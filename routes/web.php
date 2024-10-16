<?php
use App\Http\Controllers\SshController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppBotController;
Route::post('/whatsapp/webhook', [WhatsAppBotController::class, 'handleMessage']);

Route::get('/whatsapp/test', function () {
    return view('whatsapp_test');
});

// Route::get('/ssh-connect', [SshController::class, 'connect']);

Route::post('/whatsapp/test', [WhatsAppBotController::class, 'testMessage']);
Route::get('/test-api', function () {
    $response = \Illuminate\Support\Facades\Http::get('172.16.53.200'); // Adjust URL as necessary

    return $response->json(); // Return the JSON response
});

Route::get('/', function () {
    return view('welcome');
});
