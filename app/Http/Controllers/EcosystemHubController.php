<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EcosystemHubService;

class EcosystemHubController extends Controller
{
    public function syncUser(Request $request)
    {
        $validated = $request->validate([
            'legacy_user_id' => 'required',
        ]);

        $legacyUserId = $validated['legacy_user_id'];
        $user = app(EcosystemHubService::class)->syncLegacyUser($legacyUserId);

        if (!$user) {
            return response()->json([
                'success' => false,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'legacy_user_id' => $legacyUserId,
        ]);
    }
}
