<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SF001Controller extends Controller
{
    /**
     * Display Coil Stock page for SF001.
     */
    public function coilStock(): View
    {
        return view('backend.production-reports.coil-stock');
    }
}
