<?php

namespace App\Services;


use App\Models\User;
use App\Services\EcosystemHubService;
use Illuminate\Support\Str;

class MembershipCardService
{
    public function getCardData($legacyId)
    {
        // Folosește LegacyUserSyncService pentru a asigura existența userului
        $user = app(EcosystemHubService::class)->syncLegacyUser($legacyId);
        if (!$user) {
            return null;
        }
        if (empty($user->qr_token)) {
            do {
                $token = 'LCV2_' . \Illuminate\Support\Str::random(32);
            } while (User::where('qr_token', $token)->exists());
            $user->qr_token = $token;
            $user->save();
        }
        return [
            'id' => $user->id,
            'name' => $user->name,
            'legacy_user_id' => $user->legacy_user_id,
            'status' => 'ACTIVE',
            'qr_token' => $user->qr_token,
            'qr_image_url' => url('/api/qr/image/' . $user->qr_token),
        ];
    }
}
