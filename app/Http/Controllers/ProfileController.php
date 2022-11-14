<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class ProfileController extends Controller
{

    /**
     * Display the personal profile.
     *
     * @return Response
     */
    public function home()
    {
      if (!Auth::check()) return redirect('/login');
      //$this->authorize('list', Question::class);
      $user = User::find(Auth::id());
      return view('pages.profile', ['user' => $user]);
    }

}
