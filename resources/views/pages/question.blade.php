@extends('layouts.app')
@section('content')
<!-- <h2>This is a question page</h2>  --> 

<div data-id="{{$question->question_id}}" class="container">

    <div class="d-flex justify-content-between" style="padding-top: 3rem">
        <h2>{{$question->title}}</h2>
        @include('partials.question_page.question_dropdown')
    </div>
    <div class="card" style="border: none;">
        <div class="card-body;" style="font-size: 2.5rem">
            {{$question->full_text}}
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