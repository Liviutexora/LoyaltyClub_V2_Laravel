<?php

namespace App\Http\Controllers;


use App\Services\QrValidationService;

class QrValidationController extends Controller
{
    protected $qrValidationService;

    public function __construct(QrValidationService $qrValidationService)
    {
        $this->qrValidationService = $qrValidationService;
    }

    public function validateToken($token)
    {
        return response()->json(
            $this->qrValidationService->validate($token)
        );
    }
}
