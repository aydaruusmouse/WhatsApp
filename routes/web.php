<?php
use App\Http\Controllers\SshController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppBotController;

Route::get('/whatsapp/test', function () {
    return view('whatsapp_test');
});
// Change the route if you prefer API routes
// Route::post('/whatsapp/incoming', [WhatsAppBotController::class, 'handleIncomingMessage']);
// Route::get('/whatsapp/test', [WhatsAppBotController::class, 'testMessage']);


// Route::post('/whatsapp/test', [WhatsAppBotController::class, 'testMessage']);
Route::get('/test-api', function () {
    $response = \Illuminate\Support\Facades\Http::get('172.16.53.200'); // Adjust URL as necessary

    return $response->json(); // Return the JSON response
});

Route::get('/', function () {
    return view('welcome');
});
