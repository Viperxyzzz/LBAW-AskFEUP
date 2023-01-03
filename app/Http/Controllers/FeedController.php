<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Question;

class FeedController extends Controller
{

    /**
     * Shows all questions tailored to the user.
     * These are separated by recent, authored and following.
     *
     * @return mixed Returns the feed page.
     */
    public function home()
    {
      if (!Auth::check()) return redirect('/login');
      $questions['last'] = Question::orderBy('date', 'desc')->take(3)->get();
      $questions['authored'] = Auth::user()->questions()->get();
      $questions['following'] = Auth::user()->questions_following()->get();
      return view('pages.feed', ['questions' => $questions ]);
    }

}
