@extends('layouts.app')

@section('content')
  <div class="container py-5">
    <div class="row">
      <div class="col-lg-3">
        <div class="card mb-4 border-0">
          <div class="card-body text-center">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
              class="rounded-circle img-fluid" style="width: 200px;">
          </div>
        </div>
      </div>

      <div class="col-lg-8 m-0 p-0">
        <div class="card mb-4 border-0">
          <div class="card-body text-center">
            <h1>{{$user->name}}</h1>
            <h3>Score in the community: <strong>{{$user->score}}</strong></h3>
          </div>
        </div>
        <div class="d-flex justify-content-between pt-5 ml-5 pl-5">
          <h2 class="m-0">Summary</h2>
          <h3 class="m-0">{{$user->email}}</h3>
        </div>
      </div>

      <div class="col-lg-1 mt-4">
      <a class="nav-item p-2" href="{{ url('/settings') }}"> <button>Edit Profile</button></a>
      </div>
    </div>


    <div class="row pr-0">
      <div class="col-lg-3 container py-5 bg-light m-3">
        <div class="card mb-4 mb-lg-0">
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <li class="list-group-item p-3">
                <p class="mb-0">My Questions</p>
              </li>
              <li class="list-group-item p-3">
                <p class="mb-0">My Answers</p>
              </li>
              <li class="list-group-item p-3">
                <p class="mb-0">My Badges</p>
              </li>
              <li class="list-group-item p-3">
                <p class="mb-0">Following Tags</p>
              </li>
            </ul>
          </div>
        </div>
      </div>

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
