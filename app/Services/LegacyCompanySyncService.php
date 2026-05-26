<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Legacy\LegacyCompany;
use Illuminate\Support\Facades\Log;

class LegacyCompanySyncService
{
    /**
     * Ensure a Company exists for the given legacy company ID.
     *
     * @param int|string $legacyCompanyId
     * @return Company|null
     */
    public function ensureCompanyExists($legacyCompanyId)
    {
        // 1. Load LegacyCompany by legacy user id mapping
        $legacyCompany = LegacyCompany::where('id_firma', $legacyCompanyId)->first();
        if (!$legacyCompany) {
            // if not found return null
            return null;
        }

        // 2. Match company by legacy user id and upsert minimal fields
        $matchedCompany = Company::where('legacy_id', $legacyCompanyId)->first();
        if ($matchedCompany) {
            Log::info('company_sync_match_found', [
                'company_sync_match_found' => $legacyCompanyId,
                'company_id' => $matchedCompany->id,
            ]);
        }

        $company = Company::updateOrCreate(
            ['legacy_id' => $legacyCompanyId],
            [
                'name' => $legacyCompany->nume_firma,
                'email' => null,
                'company_loyalty_percent' => $legacyCompany->company_loyalty_percent,
            ]
        );

        if ($company->wasRecentlyCreated) {
            Log::info('company_sync_created', [
                'company_sync_created' => $legacyCompanyId,
                'company_id' => $company->id,
            ]);
        } else {
            Log::info('company_sync_updated', [
                'company_sync_updated' => $legacyCompanyId,
                'company_id' => $company->id,
            ]);
        }

        // 3. return company
        return $company;
    }
}
