@extends('layouts.app')

@section('content')

@include('partials.messages.feedback')

<div class="text-center m-5">
    <h1>Welcome to AskFeup</h1>
    <a class="button button-outline" href="{{ route('login') }}">Login</a>
    <a class="button" href="{{ route('register') }}">Register</a>
</div>
@endsection

