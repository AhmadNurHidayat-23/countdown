<?php

use App\Http\Controllers\CountdownController;
use App\Http\Middleware\LocalManagement;
use App\Http\Middleware\ResponseHeaders;
use Illuminate\Support\Facades\Route;

Route::middleware(ResponseHeaders::class)->group(function (): void {
    Route::get('/time', fn () => response()->json(['now' => now()->getTimestampMs()]))
        ->middleware('throttle:clock')->name('clock');
    Route::get('/c/{countdown}', [CountdownController::class, 'show'])
        ->middleware('throttle:reading')->name('countdowns.show');

    Route::middleware(LocalManagement::class)->group(function (): void {
        Route::get('/', [CountdownController::class, 'index'])->name('countdowns.index');
        Route::get('/countdowns/{countdown:manage_token}/edit', [CountdownController::class, 'edit'])->name('countdowns.edit');
        Route::middleware('throttle:management')->group(function (): void {
            Route::post('/countdowns', [CountdownController::class, 'store'])->name('countdowns.store');
            Route::put('/countdowns/{countdown:manage_token}', [CountdownController::class, 'update'])->name('countdowns.update');
            Route::delete('/countdowns/{countdown:manage_token}', [CountdownController::class, 'destroy'])->name('countdowns.destroy');
        });
    });
});
