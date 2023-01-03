@extends('layouts.app')

@section('content')

@include('partials.messages.feedback')

<div class="col-md-4 mx-auto my-5 border rounded bg-light p-5">
    <form method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        @if (session('blocked'))
            <span class="error">
            {{ session('blocked') }}
            </span>
        @endif

        @if (session('disable'))
            <span class="error">
            {{ session('disable') }}
            </span>
        @endif

        <h2>Login</h2>
        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password" >Password</label>
        <input id="password" type="password" name="password" required>
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>
            <a href="{{ route('password.request') }}" class="text-left">Forgot my password</a>
        </div>
        <div class="d-flex justify-content-center">
            <a class="button button-outline mx-1" href="{{ route('register') }}">Register</a>
            <button type="submit" class="mx-1">
                Login
            </button>
        </div>
    </form>
    <div class="d-flex flex-column justify-content-center">
        <hr class="w-50 c-secondary mx-auto">
        <a href="{{ url('/auth/redirect') }}" class="button button-outline mx-auto mt-5" style="width: 200px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg"/>
            Sign in with Google
        </a>
    </div>
</div>
@endsection
