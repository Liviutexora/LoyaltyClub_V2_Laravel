<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EcosystemHubService;

class EcosystemHubController extends Controller
{
    public function syncUser(Request $request)
    {
        $validated = $request->validate([
            'legacy_id' => 'required',
        ]);

        $legacyId = $validated['legacy_id'];
        $user = app(EcosystemHubService::class)->syncLegacyUser($legacyId);

        if (!$user) {
            return response()->json([
                'success' => false,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'legacy_id' => $legacyId,
        ]);
    }

    /**
     * Display the Mobile App page after syncing the legacy user.
     *
     * @param int|string $legacy_id
     * @return \Illuminate\View\View
     */
    public function mobileApp($legacy_id)
    {
        // Sync legacy user using EcosystemHubService
        app(EcosystemHubService::class)->syncLegacyUser($legacy_id);
        request()->session()->put('company_legacy_user_id', (int) $legacy_id);
        return view('mobile-app', [
            'legacyUserId' => $legacy_id
        ]);
    }
}
