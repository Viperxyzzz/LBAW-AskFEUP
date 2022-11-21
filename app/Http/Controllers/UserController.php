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
      $users = User::all()->sortBy('username');
      return view('pages.users', ['users' => $users]);
    }

    /**
     * Display all the users that match an exact search.
     *
     * @return Response
     */
    public function search(Request $request)
    {
      $search =  $request->input('search') ?? '';
      $users = User::search($search);
      return $this->sort_users($users, $request);
    }

    /** 
     * Sort users according to an HTTP request.
     * 
     * @return Array Users' array.
     */
    public function sort_users($users, Request $request) {
      $direction =  $request->input('direction') ?? 'asc';
      $order = $request->input('order') ?? 'username';
      
      if ($direction == 'asc') {
        $users = $users->sortBy($order)->values()->all();
      }
      else {
        $users = $users->sortByDesc($order)->values()->all();
      }
      return $users;
    }
}
