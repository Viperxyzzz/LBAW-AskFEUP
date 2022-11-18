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
        <div class="card">
          <div class="card-body">

            <div class="row">
              <ul class="d-flex list-unstyled">
                <li class="mr-4 text-center col-lg-3">
                  <div class="d-flex flex-column">
                    <strong>Nº asked<br>questions</strong>
                    <h3>{{$user->get_n_asked();}}</h3>
                  </div>
                </li>
                <li class="mr-4 text-center col-lg-5">
                  <div class="d-flex flex-column">
                    <strong>Nº answered<br>questions</strong>
                    <h3>{{$user->get_n_answered();}}</h3>
                  </div>
                </li>
                <li class="mr-4 text-center col-lg-4">
                  <div class="d-flex flex-column">
                    <strong>Nº badges<br>achieved</strong>
                    <h3>{{$user->get_n_badges();}}</h3>
                  </div>
                </li>
                <li class="mr-4 text-center col-lg-3">
                  <div class="d-flex flex-column">
                    <strong>Nº followed<br>tags</strong>
                    <h3>{{$user->get_n_ftags();}}</h3>
                  </div>
                </li>
              </ul>
            </div>

            <div class="row">
              <ul class="d-flex list-unstyled">
                <div class="col-lg-8 ml-4">
                  <li>
                    <div class="d-flex flex-column">
                      <strong>Last 3 asked questions</strong>
                      <ul class="d-flex flex-column list-unstyled ml-0">
                        @foreach($user->get_last3_asked() as $title)
                          <li>{{$title}}</li>
                        @endforeach
                      </ul>
                    </div>
                  </li>
                </div>

                <div class="col-lg-6">
                  <li>
                    <div class="d-flex flex-column">
                      <strong>Last 3 badges achieved</strong>
                      <ul class="d-flex flex-column list-unstyled">
                        @foreach($user->get_last3_badges() as $badge)
                          <li class="d-flex">
                            <span class="material-symbols-outlined">military_tech</span>
                            {{$badge}}
                          </li>
                        @endforeach
                      </ul>
                    </div>
                  </li>
                </div>

              </ul>
            </div>

          </div>
        </div>
      </div>


    </div>
  </div>
@endsection
