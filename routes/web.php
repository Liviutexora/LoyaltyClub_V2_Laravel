<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcosystemHubController;
use App\Http\Controllers\QrScannerController;
use App\Http\Controllers\ScannerLaunchController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/membership/{id}', [\App\Http\Controllers\MembershipCardController::class, 'show']);

Route::get('/mobile-app/{legacy_id}', [EcosystemHubController::class, 'mobileApp']);

Route::get('/scanner-launch/{legacy_id}', [ScannerLaunchController::class, 'launch']);

Route::get('/scanner', [QrScannerController::class, 'index']);

Route::post('/scanner/lookup', [QrScannerController::class, 'lookup']);

Route::post('/scanner/validate-transaction', function (Illuminate\Http\Request $request) {
    $payload = $request->input();
    $result = app(\App\Services\TransactionValidationService::class)
        ->validateTransaction($payload);

    return response()->json($result);
})->name('scanner.validate-transaction');