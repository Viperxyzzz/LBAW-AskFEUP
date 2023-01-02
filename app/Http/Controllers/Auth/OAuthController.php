<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class OAuthController extends Controller {

    /**
     * Redirect user to Google's authentication page
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect() {
        return Socialite::driver('google')->redirect();
    }

    /**
     * After Google account is authenticated, it will be redirected here so the user can be logged in.
     * @return mixed 
     */
    public function callback() {
        $google_user = Socialite::driver('google')->user();
        print_r($google_user);

        $user = User::firstOrCreate([
            'username' => explode("@", $google_user->email)[0]]
            ,
            ['email' => $google_user->email,
            'name' => $google_user->name,
            'score' => 0,
            'is_moderator' => 0,
            'is_admin' => 0,
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}