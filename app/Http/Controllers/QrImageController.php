<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrImageController extends Controller
{
    public function show($token)
    {
        if (empty($token)) {
            abort(404);
        }
        $svg = QrCode::format('svg')->size(300)->generate($token);
        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }
}
