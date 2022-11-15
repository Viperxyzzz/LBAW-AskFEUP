<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfileSettingsController extends Controller
{
    /**
     * Display the personal profile.
     *
     * @return Response
     */
    public function show()
    {

      return view('pages.settings');
    }
}
