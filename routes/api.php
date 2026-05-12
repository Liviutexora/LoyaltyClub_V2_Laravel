<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QrController;
use App\Http\Controllers\QrValidationController;
use App\Http\Controllers\QrImageController;
use App\Http\Controllers\MembershipCardController;
use App\Http\Controllers\QrTestRealUserController;

Route::get('/qr/user/{id}', [QrController::class, 'user']);
Route::get('/qr/validate/{token}', [QrValidationController::class, 'validateToken']);
Route::get('/qr/image/{token}', [QrImageController::class, 'show']);
Route::get('/qr/test-real-user', [QrTestRealUserController::class, 'show']);
