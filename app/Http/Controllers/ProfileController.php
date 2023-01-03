<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a user profile.
     * 
     * @param mixed $user_id The id of the profile to be displayed.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View User profile page.
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
     * Display the profile editing page.
     * 
     * @param mixed $user_id Id of the user referent to the page.
     * @return mixed Returns a profile editing page.
     */
    public function settings($user_id)
    {
      if (!Auth::check()) return redirect('/login');
      $user = User::find($user_id);
      $this->authorize('edit', $user);
      return view('pages.settings', ['user' => $user]);
    }

    /**
     * Edit a user's profile.
     * 
     * @param Request $request Request with correct user parameters.
     * @param mixed $user_id Id of user whose info is being edited.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector Redirects to user page.
     */
    public function updateUser(Request $request, $user_id){
      $user = User::find($user_id);
      $this->authorize('edit', $user);
    
      $valid_settings = $request->validate([
        'username' => 'required|string|alpha_dash|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'new_password' => 'string|min:8',
        'confirm_pass' => 'same:new_password',
        'picture_path' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
      ]);
      
    
      if(DB::table('users')->where('username', $request->username)->count() !== 0 && 
        $user->username !== $request->username
      ){
        return back()->with("alert", "This username already exists!");
      }
    
      if(DB::table('users')->where('email', $request->email)->count() !== 0 &&
         $user->email !== $request->email
        ){
        return back()->with("alert", "This email already exists!");
      }
    
      // Handle profile picture update
      if($request->hasFile('picture_path')){
        // Get file extension
        $extension = $request->file('picture_path')->getClientOriginalExtension();
        // Generate a unique file name
        $fileName = time();

        // Save the file to the public/storage/profile_pictures directory
        $request->file('picture_path')->storeAs('public/', $fileName.'.jpeg');
        // Update the picture_path field in the database
        $user->picture_path = $fileName;
        $user->save();
      }
    
      if($request->new_password === NULL){
        $user->update([
          'name' => $request->name,
          'username' => $request->username,
          'email' => $request->email
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

      return redirect('users/'.$user->user_id)->with('message', 'Changed profile information successfully!');
    }

    /**
     * Delete a users account.
     * @param mixed $user_id Id of the account to be deleted.
     * @return mixed JSON of the deleted user.
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
