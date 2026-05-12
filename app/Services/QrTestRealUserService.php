<?php

namespace App\Services;

use App\Models\User;

class QrTestRealUserService
{
    public function getRealUserWithQr()
    {
        $user = User::whereNotNull('qr_token')->first();
        if (!$user) {
            $user = User::first();
            if ($user && empty($user->qr_token)) {
                // Generate a new QR token if missing
                do {
                    $token = 'LCV2_' . \Illuminate\Support\Str::random(32);
                } while (User::where('qr_token', $token)->exists());
                $user->qr_token = $token;
                $user->save();
            }
        }
        if (!$user) {
            return null;
        }
        return [
            'id' => $user->id,
            'name' => $user->name,
            'legacy_user_id' => $user->legacy_user_id,
            'qr_token' => $user->qr_token,
            'qr_image_url' => url('/api/qr/image/' . $user->qr_token),
        ];
    }
}
