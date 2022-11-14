@extends('layouts.app')

@section('content')
<p>This is the personal feed page!</p>
<a class="button" href="{{ route('question', array('id' => 1)) }}">Question page</a>
@endsection

