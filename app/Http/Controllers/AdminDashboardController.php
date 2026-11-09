<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends DashboardController
{
    /**
     * Tampilkan Dashboard Admin
     */
    public function index(Request $request)
    {
        return parent::index($request);
    }
}
