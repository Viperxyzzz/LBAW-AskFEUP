@extends('layouts.app')
@section('content')
<!-- <h2>This is a question page</h2>  --> 

<div data-id="{{$question->question_id}}" class="container">
    <h2 style="padding-top: 3rem">{{$question->title}}</h2>
    <div class="card" style="border: none;">
        <div class="card-body;" style="font-size: 2.5rem">
            {{$question->full_text}}
        </div>
        @foreach ($answers->orderBy('num_votes', 'DESC')->get() as $answer)
                @include('partials.question_page.answer_card', ['answer' => $answer])
        @endforeach

    </div>
</div>
@endsection