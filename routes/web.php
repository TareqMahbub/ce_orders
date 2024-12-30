<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrderController::class, 'index'])->name('home');
Route::get('/stock/get', [OrderController::class, 'getStock'])->name('stock.get');
Route::put('/stock/add', [OrderController::class, 'addStock'])->name('stock.add');
