<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'halaman'])->name('halaman.tanya');
Route::post('/tanya', [MainController::class, 'tanya'])
    ->middleware('throttle:tanya-dexa')
    ->name('tanya.dexa');
