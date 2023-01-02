@extends('layouts.app')

@section('content')
<div class="col-md-4 mx-auto my-5 border rounded bg-light p-5">
  <form method="POST" action="{{ route('register') }}">
      {{ csrf_field() }}
      <h2>Register</h2>
      <label for="username">Username</label>
      <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
      @if ($errors->has('username'))
        <span class="error">
            {{ $errors->first('username') }}
        </span>
      @endif

      <label for="name">Name</label>
      <input id="name" type="text" name="name" value="{{ old('name') }}" required >
      @if ($errors->has('name'))
        <span class="error">
            {{ $errors->first('name') }}
        </span>
      @endif

      <label for="email">E-Mail Address</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required>
      @if ($errors->has('email'))
        <span class="error">
            {{ $errors->first('email') }}
        </span>
      @endif

      <label for="password">Password</label>
      <input id="password" type="password" name="password" required>
      @if ($errors->has('password'))
        <span class="error">
            {{ $errors->first('password') }}
        </span>
      @endif

      <label for="password-confirm">Confirm Password</label>
      <input id="password-confirm" type="password" name="password_confirmation" required>

      <div class="d-flex justify-content-center">
        <a class="button button-outline mx-1" href="{{ route('login') }}">Login</a>
        <button type="submit" class="mx-1">
          Register
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
