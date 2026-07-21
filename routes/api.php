<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;



Route::get('getpendidikanbystrata/{id}', [AdminController::class, 'getpendidikanbystrata']);


Route::get('get-pendidikan/{id}', [HomeController::class, 'getpendidikan']);
Route::get('get-pertanyaan/{id}', [HomeController::class, 'getpertanyaan']);
