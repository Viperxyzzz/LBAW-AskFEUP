@extends('layouts.app')

@section('content')
  <div class="container py-5">
    <div class="row">
      @include('partials.profile.image')
      @include('partials.profile.header', ['user' => $user])
      <div class="col-lg-1 mt-4">
        <a class="nav-item p-2" href="{{ url('/settings') }}"> <button>Edit Profile</button></a>
      </div>
    </div>


    <div class="row pr-0">
      @include('partials.profile.left_nav')

      <div class="col-lg-8">
        @foreach ($questions as $question)
            @include('partials.feed.question_card', ['question' => $question])
        @endforeach
    </div>
</div>
@endsection