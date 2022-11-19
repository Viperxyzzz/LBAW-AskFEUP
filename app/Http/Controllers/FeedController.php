<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\User;

class FeedController extends Controller
{

    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function home()
    {
      if (!Auth::check()) return redirect('/login');
      //$this->authorize('list', Question::class);
      $questions['last'] = Question::orderBy('date', 'desc')->take(3)->get();
      $questions['authored'] = Auth::user()->questions()->get();
      $questions['following'] = Auth::user()->questions_following()->get();
      return view('pages.feed', ['questions' => $questions ]);
    }

}
