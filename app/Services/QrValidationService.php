<?php

namespace App\Services;

use App\Models\User;

class QrValidationService
{
    public function validate($token)
    {
        $user = User::where('qr_token', $token)->first();
        if ($user) {
            return [
                'success' => true,
                'user_id' => $user->id,
                'legacy_user_id' => $user->legacy_user_id,
                'name' => $user->name,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid QR token',
            ];
        }
    }
}
