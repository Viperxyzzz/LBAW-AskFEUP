@extends('layouts.app')
@section('content')
<!-- <h2>This is a question page</h2>  --> 

<div data-id="{{$question->question_id}}" class="row">
    @include('partials.feed.left_nav')

    <div class="col-lg-7 m-3 mt-5">
        <input type="hidden" name="question_id" id="question_id" value="{{$question->question_id}}"></input>
        @include('partials.question_page.question_card', ['question' => $question])
        <div class="question-comments">
            @foreach($question->question_comments()->sortByDesc('num_votes')->all() as $comment)
                @include('partials.question_page.comment_card', ['comment' => $comment])
            @endforeach
        </div>
        @include('partials.question_page.add_answer_card', ['question_id' => $question->question_id])
        <div id="answers">
            @foreach ($answers->orderBy('num_votes', 'DESC')->get() as $answer)
                @include('partials.question_page.answer_card', ['answer' => $answer])
            @endforeach
        </div>
    </div>
</div>
@endsection