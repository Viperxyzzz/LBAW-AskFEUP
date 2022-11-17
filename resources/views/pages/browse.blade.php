@extends('layouts.app')

@section('content')
<div class="row">
    @include('partials.browse.filter')
    <div class="col-lg-6" id="questions">
        <div class="flex align-content-between mt-5">
            <h2>Results</h2>
        </div>
        <div id="questions-list">
            @foreach ($questions as $question)
                @include('partials.feed.question_card', ['question' => $question])
            @endforeach
        </div>
    </div>
</div>
@endsection

