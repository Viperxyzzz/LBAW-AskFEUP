@extends('layouts.app')

@section('content')
<div>
    <h2>This is the create question page</h2>
    <!--include the form-->
    @include('partials.feed.question_form')
    @include('partials.feed.question_tag')
</div>
@endsection