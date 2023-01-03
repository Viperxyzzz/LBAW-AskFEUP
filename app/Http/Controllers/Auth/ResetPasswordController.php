<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;

class ResetPasswordController extends Controller
{
  /**
   * Show form to ask for a password recovery.
   * 
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function show_forgot() {
    return view('auth.forgot-password');
  }

  /**
   * Send an email with a password reset link
   * @param Request $request Request with email to send link.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function send(Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
  }

  /**
   * Show the form to reset password
   * @param mixed $token Password recovery token.
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function show_reset($token) {
    return view('auth.reset-password', ['token' => $token]);
  }

  /**
   * Reset a users password
   * @param Request $request Request with token, email and password.
   * @return mixed
   */
  public function reset(Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
  }
}
