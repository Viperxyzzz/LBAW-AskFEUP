@extends('layouts.app')

@section('content')
<div class="text-center m-5">
    <h1>Welcome to <span class="c-primary">AskFeup</span></h1>
    <h4>Ask any question you want!</h4>
    <a class="" href="{{ route('browse') }}">Browse Questions</a>
</div>
<div class="container">
    <div class="row justify-content-around">
        <div class="col-sm-5">
            <div class="card card-header p-4 rounded d-flex justify-content-center align-items-center" style="min-height: 20rem">
                <a class="" href="{{ route('register') }}">
                    <h3>
                        Join the community!
                        <i class=" material-symbols-outlined" style="font-size: 3rem">chevron_right</i>
                    </h3>
                </a>
                <h6>Create an account and starting asking questions</h6>
                <a class="c-accent" href="{{ route('login') }}">I have an account</a>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="card card-header p-4 rounded d-flex justify-content-center align-items-center" style="min-height: 20rem">
                <a class="" href="{{ route('about') }}">
                    <h3>
                        Learn more
                        <i class=" material-symbols-outlined" style="font-size: 3rem">chevron_right</i>
                    </h3>
                </a>
                <h6>Learn more about this project</h6>
            </div>
        </div>
    </div>
</div>
@endsection

