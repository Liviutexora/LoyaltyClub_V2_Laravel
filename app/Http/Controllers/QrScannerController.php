<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QrLookupService;
use App\Services\ScannerContextService;

class QrScannerController extends Controller
{
    public function index(Request $request, ScannerContextService $scannerContextService)
    {
        $company = $scannerContextService->resolveCompany($request);

        return view('scanner.index', [
            'company_name' => $company?->name,
            'company_loyalty_percent' => $company?->company_loyalty_percent,
        ]);
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
