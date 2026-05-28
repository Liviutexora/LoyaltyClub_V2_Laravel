<?php

namespace App\Services;

use App\Models\LoyaltyUser;

use App\Services\EcosystemHubService;
use App\Services\ScannerContextService;

class QrLookupService
{
    public function __construct(private ScannerContextService $scannerContextService)
    {
    }

    /**
     * Find a user by QR token.
     *
     * @param string $decodedText
     * @return array|null
     */
    public function findByQr(string $decodedText)
    {
        $qrToken = trim($decodedText);

        $this->scannerContextService->ensureCompanyContextSynced(request());

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
