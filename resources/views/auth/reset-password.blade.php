@extends('layouts.app')

@section('content')

<form method="POST" action="{{ route('password.update') }}" class="col-md-4 mx-auto my-5 border rounded bg-light p-5">
    {{ csrf_field() }}
    <input type="hidden" name="token" value="{{ $token }}">
    
    <h2>Reset Password</h2>
    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
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

    <div class="d-flex justify-content-end">
        <button type="submit" class="mx-1">
            Confirm
        </button>
    </div>
</form>
@endsection