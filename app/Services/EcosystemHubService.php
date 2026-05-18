<?php

namespace App\Services;

use App\Services\LegacyUserSyncService;
use App\Models\User;

class EcosystemHubService
{
    /**
     * Sync a legacy user by ID.
     *
     * @param int|string $legacyUserId
     * @return User|null
     */
    public function syncLegacyUser($legacyUserId)
    {
        return app(LegacyUserSyncService::class)->ensureUserExists($legacyUserId);
    }
}
