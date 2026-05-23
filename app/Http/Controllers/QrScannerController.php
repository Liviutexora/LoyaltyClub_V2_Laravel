<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QrLookupService;

class QrScannerController extends Controller
{
    public function index()
    {
        return view('scanner.index');
    }

    public function lookup(Request $request, QrLookupService $qrLookupService)
    {
        $validated = $request->validate([
            'qr' => 'required|string',
        ]);

        $user = $qrLookupService->findByQr($request->qr);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
}
