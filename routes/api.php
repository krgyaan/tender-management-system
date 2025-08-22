<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TallyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test-alive', function () {
    return response()->json(['status' => 'API is working']);
});

Route::middleware('verify.tally.key')->group(function () {
    Route::get('/tally/journals', [TallyController::class, 'journal']);
    Route::get('/tally/purchases', [TallyController::class, 'purchase']);
});
