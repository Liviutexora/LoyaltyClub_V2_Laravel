<?php

namespace App\Services;

use App\Models\LoyaltyUser;

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
        $user = LoyaltyUser::where('qr_token', $qrToken)->first();
        if (!$user) {
            return null;
        }
        return [
            'name' => $user->name,
            'legacy_user_id' => $user->legacy_user_id,
            'v2_user_id' => $user->id,
        ];
    }
}
