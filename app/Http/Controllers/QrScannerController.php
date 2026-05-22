<?php

namespace App\Http\Controllers;

class QrScannerController extends Controller
{
    public function index()
    {
        return view('scanner.index');
    }
}
