<?php

use App\Http\Controllers\VideoDownloadController;
use App\Http\Controllers\DownloadStatusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

// Video download routes - main functionality on home page
Route::get('/', [VideoDownloadController::class, 'index'])->name('downloads.index');
Route::post('/downloads', [VideoDownloadController::class, 'store'])->name('downloads.store');
Route::get('/downloads/{download}', [VideoDownloadController::class, 'show'])->name('downloads.show');
Route::delete('/downloads/{download}', [VideoDownloadController::class, 'destroy'])->name('downloads.destroy');
Route::get('/downloads/status/check', [DownloadStatusController::class, 'index'])->name('downloads.status');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
