<?php

use App\Http\Controllers\MainController;
use App\Http\Middleware\EnsureAllowedAccessUrl;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'halaman'])
    ->name('halaman.tanya');
Route::post('/tanya', [MainController::class, 'tanya'])
    ->middleware(EnsureAllowedAccessUrl::class)
    ->middleware('throttle:tanya-dexa')
    ->name('tanya.dexa');

Route::post('/statistik/chat', [MainController::class, 'catatChat'])
    ->middleware(EnsureAllowedAccessUrl::class)
    ->middleware('throttle:tanya-dexa')
    ->name('statistik.chat');

Route::post('/statistik/view', [MainController::class, 'catatView'])
    ->middleware(EnsureAllowedAccessUrl::class)
    ->middleware('throttle:tanya-dexa')
    ->name('statistik.view');
