@extends('layouts.app')

@section('content')

  @include('partials.messages.feedback')

  <div class="container py-5">
    <div class="row">
      @include('partials.profile.header', ['user' => $user])
    </div>

    <div class="row pr-0 mt-5">
      @include('partials.profile.left_nav')
      <div class="col-lg-9">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="profile-tab profile-tab-open" id="user-overview-tab">
          @include('partials.profile.overview', ['user' => $user])
        </div>
        <div class="profile-tab" id="user-questions-tab">
          @include('partials.profile.questions', ['questions' => $questions])
        </div>
        <div class="profile-tab" id="user-answers-tab">
          @include('partials.profile.answers', ['answers' => $answers])
        </div>
      </div>
    </div>
  </div>
@endsection
