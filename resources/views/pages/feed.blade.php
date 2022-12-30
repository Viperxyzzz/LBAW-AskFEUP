@extends('layouts.app')

@section('content')

@include('partials.messages.feedback')

<div class="row">
    @include('partials.feed.left_nav')
    <div class="col-lg-6 " >
        <div class="flex align-content-between mt-5">
            <a class="button float-right" href="{{ route('question_create') }}">Ask a question</a>
        </div>
        <div id="last_questions">
            <h2>Last Questions</h2>
            @foreach ($questions['last'] as $question)
                @include('partials.feed.question_card', ['question' => $question])
            @endforeach
        </div>
        <div id="my_questions">
            <h2>My Questions</h2>
            @if ($questions['authored']->isEmpty())
                <p>You don't have any questions yet.
                    <a href='/question/create'>Ask a question.</a>
                </p>
            @endif
            @foreach ($questions['authored'] as $question)
                @include('partials.feed.question_card', ['question' => $question])
            @endforeach
        </div>
        <div id="following_questions">
            <h2>Questions following</h2>
            @if ($questions['following']->isEmpty())
                <p>You don't follow any questions yet.</p>
            @endif
            @foreach ($questions['following'] as $question)
                @include('partials.feed.question_card', ['question' => $question])
            @endforeach
        </div>
    </div>
    @include('partials.feed.index')
</div>
@endsection

