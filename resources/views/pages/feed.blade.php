@extends('layouts.app')

@section('content')
<div class="row">
    @include('partials.feed.left_nav')
    <div class="col-lg-6">
        <div class="flex align-content-between mt-5">
            <a class="button float-right" href="#">Ask a question</a>
            <h2>Leading Questions</h2>
        </div>
        @foreach ($questions as $question)
            @include('partials.feed.question_card', ['question' => $question])
        @endforeach
    </div>
</div>
@endsection

