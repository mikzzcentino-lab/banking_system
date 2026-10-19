<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

// Display all transactions
Route::get('/transactions', [TransactionController::class, 'index']);

// Display the transaction creation page
Route::get('/transactions/create', [TransactionController::class, 'create']);

// Save a transaction
Route::post('/transactions', [TransactionController::class, 'store']);

// Display the dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
});
