<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/membership/{id}', [\App\Http\Controllers\MembershipCardController::class, 'show']);
