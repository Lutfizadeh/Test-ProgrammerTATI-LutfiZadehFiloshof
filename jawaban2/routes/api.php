<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/get-provinsi', [ApiController::class, 'create']);
Route::get('/provinsi', [ApiController::class, 'index']);
Route::get('/provinsi/{id}', [ApiController::class, 'show']);
Route::post('/provinsi', [ApiController::class, 'store']);
Route::put('/provinsi/{id}', [ApiController::class, 'update']);
Route::delete('/provinsi/{id}', [ApiController::class, 'destroy']);