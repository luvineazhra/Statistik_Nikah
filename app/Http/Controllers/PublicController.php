<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends PublicPortalController
{
    /**
     * Halaman Utama Website Publik / Portal Data Publik
     */
    public function index(Request $request)
    {
        $response = parent::index($request);

        if (view()->exists('public.dashboard')) {
            return view('public.dashboard', $response->getData());
        }

        return $response;
    }
}
