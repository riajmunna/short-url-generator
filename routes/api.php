<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\ShortUrlGeneratorController;

Route::post('/generate-short-url',[ShortUrlGeneratorController::class, 'generateShortUrl']);
Route::get('/redirect-original-url/{shortUrl}',[ShortUrlGeneratorController::class, 'redirectToOriginalUrl']);
