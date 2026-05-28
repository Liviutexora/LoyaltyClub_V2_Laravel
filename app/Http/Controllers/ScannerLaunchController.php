<?php

namespace App\Http\Controllers;

use App\Services\EcosystemHubService;

class ScannerLaunchController extends Controller
{
    public function launch($legacy_id)
    {
        app(EcosystemHubService::class)->syncLegacyEntity($legacy_id);

        session(['company_legacy_user_id' => $legacy_id]);

        return redirect('/scanner');
    }
}
