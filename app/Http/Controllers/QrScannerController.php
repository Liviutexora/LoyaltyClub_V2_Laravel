<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Services\EcosystemHubService;
use App\Services\QrLookupService;

class QrScannerController extends Controller
{
    public function index()
    {
        $companyLegacyUserId = request()->session()->get('company_legacy_user_id');
        if (!$companyLegacyUserId) {
            $companyLegacyUserId = request()->query('company_legacy_user_id')
                ?? request()->query('legacy_id');
            if (!empty($companyLegacyUserId)) {
                request()->session()->put('company_legacy_user_id', (int) $companyLegacyUserId);
            }
        }
        if (!$companyLegacyUserId && Auth::check()) {
            $companyLegacyUserId = Auth::user()->legacy_id ?? null;
        }

        $company = null;
        if (!empty($companyLegacyUserId)) {
            app(EcosystemHubService::class)->syncLegacyEntity($companyLegacyUserId);
            $company = Company::where('legacy_id', $companyLegacyUserId)->first();
        }

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
