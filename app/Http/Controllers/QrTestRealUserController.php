<?php

namespace App\Http\Controllers;

use App\Services\QrTestRealUserService;

class QrTestRealUserController extends Controller
{
    protected $service;

    public function __construct(QrTestRealUserService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        $data = $this->service->getRealUserWithQr();
        if (!$data) {
            return response()->json(['error' => 'No user found'], 404);
        }
        return response()->json($data);
    }
}
