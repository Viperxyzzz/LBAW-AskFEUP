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
    public function home()
    {
      if (!Auth::check()) return redirect('/login');
      //$this->authorize('list', Question::class);
      $user = User::find(Auth::id());
      return view('pages.profile', ['user' => $user]);
    }

    /**
     * Display the personal profile.
     *
     * @return Response
     */
    public function settings()
    {
      if (!Auth::check()) return redirect('/login');
      //$this->authorize('list', Question::class);
      $user = User::find(Auth::id());
      return view('pages.settings', ['user' => $user]);
    }

    /**
     * Display the personal profile.
     *
     * @return Response
     */
    public function myQuestions()
    {
      if (!Auth::check()) return redirect('/login');
      //$this->authorize('list', Question::class);
      $user = User::find(Auth::id());
      $questions = $user->questions()->orderBy('question_id', 'DESC')->get();
      return view('pages.my_questions', ['user' => $user, 'questions' => $questions]);
    }

    /**
     * Display the personal profile.
     *
     * @return Response
     */
    public function myAnswers()
    {
      if (!Auth::check()) return redirect('/login');
      //$this->authorize('list', Question::class);
      $user = User::find(Auth::id());
      $answers = $user->answers()->orderBy('answer_id', 'DESC')->get();
      return view('pages.my_answers', ['user' => $user, 'answers' => $answers]);
    }
    
    public function updateUser(Request $request){
      $new_password = $request->new_password;
      $confirm_pass = $request->confirm_pass;

      if($new_password !== $confirm_pass){
        return back()->with("error", "Passwords didn' match!");
      }

      if($new_password === NULL){
        auth()->user()->update([
          'name' => $request->name,
          'username' => $request->username,
          'email' => $request->email,
        ]);
      }

      else {
        auth()->user()->update([
          'name' => $request->name,
          'username' => $request->username,
          'email' => $request->email,
          'password' => Hash::make($request->new_password)
        ]);
      }

      return back()->with("status", "Settings edited successfully!");
      
    }

}
