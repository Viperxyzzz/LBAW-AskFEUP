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

        @foreach ($answers as $answer)
          <h3>
            <strong>Question:</strong>
            <a href="{{route('question', array('id' => $answer->question()->get()->value('question_id'))) }}">
              {{$answer->question()->get()->value('title')}}
            </a>
          </h3>
          @include('partials.question_page.answer_card', ['answer' => $answer])
        @endforeach
    </div>
</div>
@endsection