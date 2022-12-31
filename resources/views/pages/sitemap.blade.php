@extends('layouts.app')

@section('content')

<h3 class="m-5 text-center"><span class="c-primary">AskFeup</span>'s Sitemap</h3>
<div class="d-flex flex-wrap justify-content-center sitemap text-center">
    <div class="m-5">
        <h4>Questions</h4>
        <ul>
            @if (Auth::check())
                <li><a href="{{ route('feed') }}">Feed</a></li>
            @endif
            <li><a href="{{ route('question_create') }}">New Question</a></li>
            <li><a href="{{ route('browse') }}">Browse questions</a></li>
        </ul>
    </div>
    <div class="m-5">
        <h4>Users</h4>
        <ul>
            <li><a href="{{ url('users') }}">Browse users</a></li>
            @if (Auth::check())
            <li><a href="{{ url("users/" . strval(Auth::id()) ) }}">Profile</a></li>
            @endif
        </ul>
    </div>
    <div class="m-5">
        <h4>Tags</h4>
        <ul>
            <li><a href="{{ url('tags') }}">Browse tags</a></li>
        </ul>
    </div>
    <div class="m-5">
        <h4>Authentication</h4>
        <ul>
            <li><a href="{{ url('login') }}">Login</a></li>
            <li><a href="{{ url('register') }}">Register</a></li>
        </ul>
    </div>
    <div class="m-5">
        <h4>About</h4>
        <ul>
            <li><a href="{{ url('about') }}">About us</a></li>
            <li><a href="{{ url('sitemap') }}">Sitemap</a></li>
        </ul>
    </div>
    @if (Auth::check())
        @if(Auth::user()->is_mod())
            <div class="m-5">
                <h4>Moderation</h4>
                <ul>
                    <li><a href="{{ url('dashboard') }}">Dashboard</a></li>
                </ul>
            </div>
        @endif
    @endif
</div>

@endsection