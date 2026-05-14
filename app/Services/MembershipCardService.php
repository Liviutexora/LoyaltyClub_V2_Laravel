<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Str;

class MembershipCardService
{
    public function getCardData($legacyId)
    {
        // Caută user după legacy_user_id
        $user = User::where('legacy_user_id', $legacyId)->first();
        if (!$user) {
            // Încearcă să citești din baza legacy
            $legacyUser = \App\Models\LegacyUser::find($legacyId);
            if (!$legacyUser) {
                return null;
            }
            // Creează user nou în V2
            $user = new User();
            $user->legacy_user_id = $legacyUser->id;
            $user->name = $legacyUser->nume;
            $user->email = $legacyUser->email;
            // Parolă randomă securizată (nu se folosește la login)
            $user->password = bcrypt(Str::random(32));
            $user->save();
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
