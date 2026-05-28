<?php

namespace App\Services;

use App\Models\Company;
use App\Services\EcosystemHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerContextService
{
    /**
     * Ensure scanner company context is resolved and synced.
     */
    public function ensureCompanyContextSynced(Request $request): void
    {
        $this->resolveCompany($request);
    }

    /**
     * Resolve active scanner company context from session/query/auth and ensure sync.
     */
    public function resolveCompany(Request $request): ?Company
    {
        $companyLegacyUserId = $request->session()->get('company_legacy_user_id');
        if (!$companyLegacyUserId) {
            $companyLegacyUserId = $request->query('company_legacy_user_id')
                ?? $request->query('legacy_id');
            if (!empty($companyLegacyUserId)) {
                $request->session()->put('company_legacy_user_id', (int) $companyLegacyUserId);
            }
        }

        if (!$companyLegacyUserId && Auth::check()) {
            $companyLegacyUserId = Auth::user()->legacy_id ?? null;
        }

        if (empty($companyLegacyUserId)) {
            return null;
        }

        app(EcosystemHubService::class)->syncLegacyEntity($companyLegacyUserId);

        return Company::where('legacy_id', $companyLegacyUserId)->first();
    }
}
