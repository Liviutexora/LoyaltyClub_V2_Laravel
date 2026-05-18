<?php

namespace App\Services;

use App\Models\User;
use App\Models\Legacy\LegacyUser;
use Illuminate\Support\Str;

class LegacyUserSyncService
{
    /**
     * Ensure a User exists for the given legacy user ID.
     *
     * @param int|string $legacyId
     * @return User|null
     */
    public function ensureUserExists($legacyId)
    {
        // 1. Search User by legacy_user_id
        $user = User::where('legacy_user_id', $legacyId)->first();
        if ($user) {
            // 2. If exists -> return user
            return $user;
        }

        // 3. If not exists: load LegacyUser by id
        $legacyUser = LegacyUser::find($legacyId);
        if (!$legacyUser) {
            // if not found return null
            return null;
        }

        // create new User
        $user = new User();
        $user->legacy_user_id = $legacyUser->id;
        $user->name = $legacyUser->nume;
        $user->email = $legacyUser->email;
        // generate random password
        $user->password = bcrypt(Str::random(32));
        $user->save();

        // 4. return user
        return $user;
    }
}
