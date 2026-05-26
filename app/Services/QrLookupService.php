<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\LoyaltyUser;

use App\Services\EcosystemHubService;

class QrLookupService
{
    /**
     * Find a user by QR token.
     *
     * @param string $decodedText
     * @return array|null
     */
    public function findByQr(string $decodedText)
    {
        $qrToken = trim($decodedText);

        $companyLegacyUserId = request()->session()->get('company_legacy_user_id');
        if (!$companyLegacyUserId && Auth::check()) {
            $companyLegacyUserId = Auth::user()->legacy_id ?? null;
        }
        Log::info('company_sync_pre_lookup', ['company_sync_pre_lookup' => $companyLegacyUserId]);
        if (!empty($companyLegacyUserId)) {
            app(EcosystemHubService::class)->syncLegacyEntity($companyLegacyUserId);
        }

        $user = LoyaltyUser::where('token', $qrToken)->first();
        if (!$user) {
            return null;
        }

        app(EcosystemHubService::class)->syncLegacyEntity($user->legacy_id);
        return [
            'name' => $user->name,
            'legacy_id' => $user->legacy_id,
            'v2_user_id' => $user->id,
        ];
    }
}
