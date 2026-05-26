<?php

namespace App\Services;

use App\Models\User;

class QrTestRealUserService
{
    public function getRealUserWithQr()
    {
        $user = User::whereNotNull('token')->first();
        if (!$user) {
            $user = User::first();
            if ($user && empty($user->token)) {
                // Generate a new QR token if missing
                do {
                    $token = 'LCV2_' . \Illuminate\Support\Str::random(32);
                } while (User::where('token', $token)->exists());
                $user->token = $token;
                $user->save();
            }
        }
        if (!$user) {
            return null;
        }
        return [
            'id' => $user->id,
            'name' => $user->name,
            'legacy_id' => $user->legacy_id,
            'token' => $user->token,
            'qr_image_url' => url('/api/qr/image/' . $user->token),
        ];
    }
}
