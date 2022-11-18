@extends('layouts.app')
@section('content')

<div class="row m-5">
    <div class="col-lg-2">
        <!-- TODO filter -->
    </div>
    <div class="col-lg-10">
        <h2>Users</h2>
        <input id="user-search" name="user-search" value="" autocomplete="off" class="col-sm-3" type="text" placeholder="Search...">
        <div id="users-list" class="d-flex flex-wrap">
            @foreach ($users as $user)
                @include('partials.users.user_card', ['users' => $users])
            @endforeach
        </div>
    </div>
</div>

@endsection