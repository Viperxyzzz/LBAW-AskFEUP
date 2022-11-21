<div class="d-flex border p-5 col-md-12">
    <img src="{{asset('storage/'.($user->picture_path).'.jpeg')}}" alt="avatar"
              class="rounded-circle">
    <div class="d-flex justify-content-between w-100">
        <div>
            <h1 class="m-0 mt-2 ml-5">{{$user->name}}</h1>
            <h4 class="text-secondary ml-5"><em>{{$user->username}}</em></h4>
            <h4 class="ml-5">{{$user->email}}</h4>
        </div>
        <div>
            @can('edit', $user)
                <a href="{{ url('/settings', ['id' => $user->user_id]) }}"> <button>Edit Profile</button></a>
            @endcan
            <h4 class="text-right"><strong class="title-blue">{{$user->score}}</strong> points</h4>
        </div>
    </div>
</div>