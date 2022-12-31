<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Question;

class ProfileController extends Controller
{

    /**
     * Display the personal profile.
     *
     * @return Response
     */
    public function home($user_id)
    {
      $user = User::find($user_id);
      $this->authorize('view', $user);
      $questions = $user->questions()->orderBy('question_id', 'DESC')->get();
      $answers = $user->answers()->orderBy('answer_id', 'DESC')->get();
      $tags = $user->tags_following()->get();
      return view('pages.profile', [
        'user' => $user,
        'questions' => $questions,
        'answers' => $answers,
        'tags' => $tags
      ]);
    }

    /**
     * Display the personal profile.
     *
     * @return Response
     */
    public function settings($user_id)
    {
      if (!Auth::check()) return redirect('/login');
      $user = User::find($user_id);
      $this->authorize('edit', $user);
      return view('pages.settings', ['user' => $user]);
    }

    public function updateUser(Request $request, $user_id){
      $user = User::find($user_id);
      $this->authorize('edit', $user);

      $valid_settings = $request->validate([
        'username' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'new_password' => 'string|min:6',
        'confirm_pass' => 'same:new_password',
      ]);

      if(DB::table('users')->where('username', $request->username)->count() !== 0 && 
        $user->username !== $request->username
      ){
        return back()->with("error", "This username already exists!");
      }

      if(DB::table('users')->where('email', $request->email)->count() !== 0 &&
         $user->email !== $request->email
        ){
        return back()->with("error", "This email already exists!");
      }

      if($request->new_password === NULL){
        $user->update([
          'name' => $request->name,
          'username' => $request->username,
          'email' => $request->email,
        ]);
      }

      else {
        $user->update([
          'name' => $request->name,
          'username' => $request->username,
          'email' => $request->email,
          'password' => Hash::make($request->new_password)
        ]);
      }

      return redirect('users/'.$user->user_id);
    }

    /**
     * Delete an account
     */
    public function delete($user_id)
    {
      // account updates
      $user = User::find($user_id);
      $this->authorize('delete', $user);

      $user->name = 'anonymous';
      $user->picture_path = 'guest';

      $user->save();

    return $user;
    }

}