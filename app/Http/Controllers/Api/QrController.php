<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoyaltyUser;

class QrController extends Controller
{
    public function user($id)
    {
        $user = LoyaltyUser::find($id);
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }
    }
}
