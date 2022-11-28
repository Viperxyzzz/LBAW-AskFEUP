@extends('layouts.app')
@section('content')
<!-- <h2>This is a question page</h2>  --> 

<div data-id="{{$question->question_id}}" class="row">
    @include('partials.feed.left_nav')

    <div class="col-lg-7 mt-5">
        @include('partials.question_page.question_card', ['question' => $question])
        @include('partials.question_page.add_answer_card', ['question_id' => $question->question_id])
        <div id="answers">
            @foreach ($answers->orderBy('num_votes', 'DESC')->get() as $answer)
                @include('partials.question_page.answer_card', ['answer' => $answer])
            @endforeach
        </div>
    </div>
</div>
@endsection