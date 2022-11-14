@extends('layouts.app')

@section('content')
<div class="container w-100 mx-0">
    <div class="row">
        @include('partials.feed.left_nav')
        <div class="col-sm-8">
            <a class="button" href="{{ route('question', array('id' => 1)) }}">Question page</a>
            @foreach ($questions as $question)
                @include('partials.feed.question_card', ['question' => $question])
            @endforeach
        </div>
    </div>
</div>
@endsection

