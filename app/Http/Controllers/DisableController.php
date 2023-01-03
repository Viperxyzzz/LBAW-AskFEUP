<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Block;
use App\Models\Disable;

class DisableController extends Controller
{
    //

    /**
     * Store a newly created block.
     *
     * @param  \Illuminate\Http\Request  $request HTTP Post Request.
     * @return \Illuminate\Http\Response Disable json object.
     */
    public function store($user_id)
    {
        if(!Auth::check()) return redirect('/login');
        $this->authorize('create', Disable::class);

        $disable_user = new Disable;
        $disable_user->user_id = $user_id;
        $disable_user->date = date('Y-m-d H:i:s');

        $disable_user->save();
        
        auth()->logout();
        
        return $disable_user;
    }
}