<?php

namespace App\Services;

use App\Models\Legacy\LegacyUser;
use App\Models\Company;
use App\Models\User;
use App\Services\LegacyCompanySyncService;
use App\Services\LegacyUserSyncService;
use Illuminate\Support\Facades\Http;

class EcosystemHubService
{
    /**
     * Sync a legacy entity by ID based on legacy user tip.
     *
     * @param int|string $legacyId
     * @return User|Company|null
     */
    public function syncLegacyEntity($legacyId)
    {
        $legacyUser = LegacyUser::find($legacyId);
        if (!$legacyUser) {
            return null;
        }

        $legacyType = (int) $legacyUser->tip;

        if ($legacyType === 1) {
            return app(LegacyUserSyncService::class)->ensureUserExists($legacyId);
        }

        if ($legacyType === 2) {
            return app(LegacyCompanySyncService::class)->ensureCompanyExists($legacyId);
        }

        return null;
    }

    /**
     * Backward-compatible wrapper for legacy user sync.
     *
     * @param int|string $legacyUserId
     * @return User|Company|null
     */
    public function syncLegacyUser($legacyUserId)
    {
        return $this->syncLegacyEntity($legacyUserId);
    }

    /**
     * Forward transaction validation payload to the bridge layer.
     */
    public function forwardTransactionValidation(array $payload): array
    {
        $url = config('services.legacy_bridge.transaction_validation_url');

        if (empty($url)) {
            return [
                'success' => false,
                'message' => 'Legacy bridge URL not configured',
            ];
        }

        try {
            $response = Http::asForm()->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->body(),
                ];
            }

            $contentType = (string) $response->header('Content-Type', '');
            $bridgeMessage = null;

            if (stripos($contentType, 'application/json') !== false) {
                $bridgeMessage = $response->json('message');
            }

            $bridgeMessage = is_string($bridgeMessage) && trim($bridgeMessage) !== ''
                ? $bridgeMessage
                : $response->body();

            return [
                'success' => false,
                'message' => trim((string) $bridgeMessage) !== ''
                    ? $bridgeMessage
                    : 'Bridge request failed',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
