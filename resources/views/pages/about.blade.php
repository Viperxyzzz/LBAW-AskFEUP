@extends('layouts.app')

@section('content')
<div class="row m-5">
    <div class="col-md-4 offset-md-2">
        <h3>About</h3>
        <p class="">
            <span class="c-primary">AskFeup</span> is a curricular project developed for the Database and Web Applications Laboratory Curricular Unit.
            It is a <strong>collaborative</strong> question system. It allows for people to ask questions about anything, provide answers and create collective knowledge.
        </p>
    </div>
    <section class="col-md-4">
        <header class="d-flex justify-content-between align-items-baseline">
            <h3>Contacts</h3>
            <p class="text-right m-0"><em>lbaw2221</em></p>
        </header>
        <ul class="list-group ist-group-flush">
            <li class="list-group-item d-flex justify-content-between">Igor Diniz<a href = "mailto:up202000162@edu.fe.up.pt">up202000162@edu.fe.up.pt</a></li>
            <li class="list-group-item d-flex justify-content-between">Isabella Colombarolli<a href = "mailto:up201902617@edu.fe.up.pt">up201902617@edu.fe.up.pt</a></li>
            <li class="list-group-item d-flex justify-content-between">José Luís Rodrigues <a href = "mailto:up202008462@edu.fe.up.pt">up202008462@edu.fe.up.pt</a></li>
            <li class="list-group-item d-flex justify-content-between">Pedro Nunes<a href = "mailto:up201905396@edu.fe.up.pt">up201905396@edu.fe.up.pt</a></li>
        </ul>
    </section>
</div>

<div class="col-md-8 offset-md-2">
    <h3 class="text-center">Implemented Features</h3>
    <li class="d-flex flex-wrap justify-content-center">
        <ul class="card card-header p-3 m-1">View Top Questions</ul>
        <ul class="card card-header p-3 m-1">View Recent Questions</ul>
        <ul class="card card-header p-3 m-1">Browse Questions</ul>
        <ul class="card card-header p-3 m-1">Browse Questions by Tags</ul>
        <ul class="card card-header p-3 m-1">View Question Details</ul>
        <ul class="card card-header p-3 m-1">View User Profiles</ul>
        <ul class="card card-header p-3 m-1">Post Question</ul>
        <ul class="card card-header p-3 m-1">Post Answer</ul>
        <ul class="card card-header p-3 m-1">Vote on Questions</ul>
        <ul class="card card-header p-3 m-1">Vote on Answers</ul>
        <ul class="card card-header p-3 m-1">Comment on Questions</ul>
        <ul class="card card-header p-3 m-1">Comment on Answers</ul>
        <ul class="card card-header p-3 m-1">View My Questions</ul>
        <ul class="card card-header p-3 m-1">View My Answers</ul>
        <ul class="card card-header p-3 m-1">Follow Question</ul>
        <ul class="card card-header p-3 m-1">Follow Tags</ul>
        <ul class="card card-header p-3 m-1">Follow Question</ul>
        <ul class="card card-header p-3 m-1">Report Content</ul>
        <ul class="card card-header p-3 m-1">Support User Badges</ul>
        <ul class="card card-header p-3 m-1">Edit Question</ul>
        <ul class="card card-header p-3 m-1">Support User Badges</ul>
        <ul class="card card-header p-3 m-1">Delete Question</ul>
        <ul class="card card-header p-3 m-1">Edit Answer</ul>
        <ul class="card card-header p-3 m-1">Edit Comment</ul>
        <ul class="card card-header p-3 m-1">Delete Answer</ul>
        <ul class="card card-header p-3 m-1">Delete Comment</ul>
        <ul class="card card-header p-3 m-1">Edit Question Tags</ul>
        <ul class="card card-header p-3 m-1">Mark Answer as Correct</ul>
        <ul class="card card-header p-3 m-1">Edit Question Tags</ul>
        <ul class="card card-header p-3 m-1">Manage Content Reports</ul>
        <ul class="card card-header p-3 m-1">Notifications</ul>
        <ul class="card card-header p-3 m-1">Badges</ul>
        <ul class="card card-header p-3 m-1">Badge supporting</ul>
        <ul class="card card-header p-3 m-1">Tag managment</ul>
        <ul class="card card-header p-3 m-1">Report managment</ul>
        <ul class="card card-header p-3 m-1">Account managment</ul>
        <ul class="card card-header p-3 m-1">Recover password</ul>
        <ul class="card card-header p-3 m-1">Delete account</ul>
        <ul class="card card-header p-3 m-1">Block users</ul>
        <ul class="card card-header p-3 m-1">Profile pictures</ul>
        <ul class="card card-header p-3 m-1">Text search</ul>
        <ul class="card card-header p-3 m-1">Google OAuth</ul>
    </li>
</div>


@endsection
