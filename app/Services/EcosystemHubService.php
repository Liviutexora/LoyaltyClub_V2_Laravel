<?php

namespace App\Services;

use App\Models\Legacy\LegacyUser;
use App\Models\Company;
use App\Models\User;
use App\Services\LegacyCompanySyncService;
use App\Services\LegacyUserSyncService;

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
}
