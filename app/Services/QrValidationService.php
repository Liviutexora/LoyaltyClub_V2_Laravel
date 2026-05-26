<?php

namespace App\Services;

use App\Models\User;

class QrValidationService
{
    public function validate($token)
    {
        $user = User::where('token', $token)->first();
        if ($user) {
            return [
                'success' => true,
                'user_id' => $user->id,
                'legacy_id' => $user->legacy_id,
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
