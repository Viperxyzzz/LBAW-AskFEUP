<h3>Badges</h3>
<div class="d-flex flex-wrap">
@foreach($user->badges() as $user_badge)
    <div class=" border-strong rounded col-md-5 m-2 p-3 d-flex flex-row justify-content-between">
        <div>
            <h4 class="c-primary">{{ $user_badge->badge()->badge_name }}</h4>
            <p>{{ \Carbon\Carbon::parse($user_badge->date)->diffForHumans() }}</p>
            <div class="d-flex align-items-end">
                <i class="mr-2 mb-1 material-symbols-outlined">favorite</i>
                <p class="m-0">{{ $user_badge->num_supports }} </p>

            </div>
        </div>
        <img src={{ asset('storage/badges/' . $user_badge->badge()->badge_id . '.png') }}>
    </div>
@endforeach
</div>