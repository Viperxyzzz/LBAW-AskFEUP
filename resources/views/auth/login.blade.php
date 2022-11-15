@extends('layouts.app')

@section('content')

<form method="POST" action="{{ route('login') }}" class="w-25 mx-auto my-5 border rounded bg-light p-5">
    {{ csrf_field() }}
    
    <h2>Login</h2>
    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
    @if ($errors->has('email'))
        <span class="error">
          {{ $errors->first('email') }}
        </span>
    @endif

    <label for="password" >Password</label>
    <input id="password" type="password" name="password" required>
    @if ($errors->has('password'))
        <span class="error">
            {{ $errors->first('password') }}
        </span>
    @endif

    <label>
        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
    </label>

    <div class="d-flex justify-content-end">
        <a class="button button-outline mx-1" href="{{ route('register') }}">Register</a>
        <button type="submit" class="mx-1">
            Login
        </button>
    </div>
</form>
@endsection
