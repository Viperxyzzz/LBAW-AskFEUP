<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{

    /**
     * Display all the users.
     *
     * @return mixed Page that displayed the results of the queried users.
     */
    public function home()
    {
      $users = User::all()->sortBy('username');
      $filtered_user = $users->filter(function ($item) {
        return (!$item->is_disable());
      });
        return view('pages.users', ['users' => $filtered_user]);
    }

    /**
     * Display all the users that match an exact search.
     *
     * @return mixed Page that displayed the results of the queried users.
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
