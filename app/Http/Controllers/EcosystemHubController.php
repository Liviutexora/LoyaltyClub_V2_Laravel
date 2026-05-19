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

    /**
     * Display the Mobile App page after syncing the legacy user.
     *
     * @param int|string $legacy_user_id
     * @return \Illuminate\View\View
     */
    public function mobileApp($legacy_user_id)
    {
        // Sync legacy user using EcosystemHubService
        app(EcosystemHubService::class)->syncLegacyUser($legacy_user_id);
        return view('mobile-app', [
            'legacyUserId' => $legacy_user_id
        ]);
    }
}
