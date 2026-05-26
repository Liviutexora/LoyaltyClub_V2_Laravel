<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcosystemHubController;
use App\Http\Controllers\QrScannerController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/membership/{id}', [\App\Http\Controllers\MembershipCardController::class, 'show']);

Route::get('/mobile-app/{legacy_id}', [EcosystemHubController::class, 'mobileApp']);

Route::get('/scanner', [QrScannerController::class, 'index']);

Route::post('/scanner/lookup', [QrScannerController::class, 'lookup']);