@extends('layouts.app')
@section('content')

<div class="row">
    @include('partials.users.filter')
    <div class="col-lg-10 mt-5">
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