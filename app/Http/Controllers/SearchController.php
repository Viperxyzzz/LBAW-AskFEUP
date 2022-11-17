<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;

class SearchController extends Controller
{
    /**
     * Get a set of filtered and ordered questions as requested.
     * 
     * @param Request HTTP request with optional query params 'order' and 'search'.
     * @return Array Array of questions.
     */
    public function get_questions(Request $request) {
      if (is_null($request->input('order'))) {
        $order = 'date';
      } else {
        $order = $request->input('order');
      }
      // TODO if search
      $questions = Question::orderBy($order, 'desc')->get();
      foreach($questions as $question) {
        $question['author_name'] = $question->author->name;
        $question['date_distance'] = $question->date_distance();
        $question['tags'] = $question->tags()->orderBy('tag_id')->get();
      }
      return $questions;
    }

    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function home(Request $request)
    {
      $questions = $this->get_questions($request);
      return view('pages.browse', ['questions' => $questions ]);
    }

    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function browse(Request $request)
    {
      $questions = $this->get_questions($request);
      return json_encode($questions);
    }

}
