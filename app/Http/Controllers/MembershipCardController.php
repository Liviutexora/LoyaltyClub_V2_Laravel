<?php

namespace App\Http\Controllers;

use App\Services\MembershipCardService;

class MembershipCardController extends Controller
{
    protected $service;

    public function __construct(MembershipCardService $service)
    {
        $this->service = $service;
    }

    public function show($id)
    {
        $data = $this->service->getCardData($id);
        if (!$data) {
            abort(404);
        }
        return view('membership.card', $data);
    }
}
