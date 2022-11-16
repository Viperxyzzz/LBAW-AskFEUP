@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-lg-6">
        <div class="mt-5 p-3">
            <h2>Ask a question</h2>
        </div>
        <!--include the form-->
        <form method="POST" action="{{ route('question_create_api') }}" enctype="multipart/form-data">
            @csrf
            @include('partials.create_question.question_form')
            @include('partials.create_question.question_tags')
            
            <button class="my-5" type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection