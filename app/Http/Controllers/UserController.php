<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserController extends Controller
{

    /**
     * Display all the users.
     *
     * @return Response
     */
    public function home()
    {
      $users = User::all();
      return view('pages.users', ['users' => $users]);
    }

    /**
     * Display all the users that match an exact search.
     *
     * @return Response
     */
    public function all()
    {
      return User::all();
    }

    /**
     * Display all the users that match an exact search.
     *
     * @return Response
     */
    public function search($search)
    {
      return User::search($search);
    }
}
