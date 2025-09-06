<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;


Route::get('/api/tickets', [ApiController::class, 'index'])->name('tickets.index');
Route::post('/api/Newtickets', [ApiController::class, 'store'])->name('tickets.store');
Route::get('/api/tickets/{id}', [ApiController::class, 'show'])->name('tickets.show');
Route::patch('/api/tickets/{id}', [ApiController::class, 'update'])->name('tickets.update');
Route::post('/api/tickets/{id}/classify', [ApiController::class, 'classify'])->name('tickets.classify');
Route::get('/api/stats', [ApiController::class, 'stats'])->name('tickets.stats');



Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
