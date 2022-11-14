@extends('layouts.app')

@section('content')
<div class="container w-100 mx-0">
    <div class="row">
        @include('partials.feed.left_nav')
        <div class="col-sm-8">
            @foreach ($questions as $question)
                @include('partials.feed.question_card', ['question' => $question])
            @endforeach
        </div>
    </div>
</div>
@endsection

