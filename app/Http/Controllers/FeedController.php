<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;

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
      $questions['authored'] = User::questions()->get();
      $questions['following'] = User::questions()->get();
      return view('pages.feed', ['questions' => $questions ]);
    }

}
