@extends('layouts.app')

@section('content')

<form method="POST" action="{{ route('password.email') }}" class="col-md-4 mx-auto my-5 border rounded bg-light p-5">
    {{ csrf_field() }}
    
    <h2>Forgot Password</h2>
    <p>Tell your account's email so we can send a reset link.</p>
    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
    @if ($errors->has('email'))
        <span class="error">
          {{ $errors->first('email') }}
        </span>
    @endif

    <div class="d-flex justify-content-end">
        <a class="button button-outline mx-1" href="{{ route('login') }}">Login</a>
        <button type="submit" class="mx-1">
            Send
        </button>
    </div>
</form>
@endsection