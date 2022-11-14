@extends('layouts.app')

@section('content')
<div data-id="{{$question->question_id}}">
    <h2>This is a question page</h2>
    <h3>Title: {{$question->title}}</h3>
</div>
@endsection