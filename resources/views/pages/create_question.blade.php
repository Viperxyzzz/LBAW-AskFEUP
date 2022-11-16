@extends('layouts.app')

@section('content')
<div>
    <h2>This is the create question page</h2>
    <!--include the form-->
    <form method="POST" action="{{ route('question_create_api') }}" enctype="multipart/form-data">
        @csrf
        @include('partials.create_question.question_form')

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection