@extends('layouts.app')

@section('content')
@include('partials.messages.feedback')

<div class="col-md-4 mx-auto my-5 border rounded bg-light p-5">
  <form method="POST" action="{{ route('register') }}">
      {{ csrf_field() }}
      <h2>Register</h2>
      <label for="username">Username</label>
      <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>

      <label for="name">Name</label>
      <input id="name" type="text" name="name" value="{{ old('name') }}" required >

      <label for="email">E-Mail Address</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required>

      <label for="password">Password</label>
      <input id="password" type="password" name="password" required>

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
