@extends('layouts.app')

@include('partials.messages.feedback')

@section('content')
<div class="row">
    @include('partials.browse.filter')
    <div class="col-lg-6 m-3" id="questions">
        <div class="flex align-content-between mt-5">
            <h2>Results</h2>
        </div>
        <div id="questions-list">
            @if ($questions->isEmpty())
                <p>No questions found!</p>
            @endif
            @foreach ($questions as $question)
                    @include('partials.feed.question_card', ['question' => $question])
            @endforeach
        </div>
    </div>
</div>
@endsection

