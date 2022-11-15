@extends('layouts.app')

@section('content')
<!-- <h2>This is a question page</h2>  --> 
<div data-id="{{$question->question_id}}" class="container">
    <h2>{{$question->title}}</h2>
    <div class="card">
        <div class="card-body">
            {{$question->full_text}}
        </div>
        @foreach ($answers->orderBy('answer_id')->get() as $answer)
                @include('partials.question_page.answer_card', ['answer' => $answer])
        @endforeach
    </div>
</div>
@endsection