<div class="d-flex border p-5 col-md-12">
    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
            class="rounded-circle img-fluid" style="width: 200px;">
    <div class="d-flex justify-content-between w-100">
        <div>
            <h1 class="m-0 mt-2">{{$user->name}}</h1>
            <h3 class="text-secondary"><em>{{$user->username}}</em></h3>
            <h4>{{$user->email}}</h4>
        </div>
        <div>
            @if (Auth::id() === $user->user_id)
                <a href="{{ url('/settings') }}"> <button>Edit Profile</button></a>
            @endif
            <h4 class="text-right"><strong class="title-blue">{{$user->score}}</strong> points</h4>
        </div>
    </div>
</div>