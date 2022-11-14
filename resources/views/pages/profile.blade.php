@extends('layouts.app')

@section('content')
<div>
    <h2>This is the profile page</h2>
    <h3>Name: {{$user->name}}</h3>
    <h3>Email: {{$user->email}}</h3>
</div>
@endsection