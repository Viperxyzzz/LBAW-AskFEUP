<h3>Badges</h3>
<div class="d-flex flex-wrap">
@foreach($user->badges() as $user_badge)
    <div class=" border-strong rounded col-md-5 m-2 p-3 d-flex flex-row justify-content-between">
        <div>
            <h4 class="c-primary">{{ $user_badge->badge()->badge_name }}</h4>
            <p>{{ \Carbon\Carbon::parse($user_badge->date)->diffForHumans() }}</p>
            <div class="d-flex align-items-end">
                <button class="badge-button p-0 d-flex align-items-end">
                    @if (Auth::user()->supports_badge($user_badge->badge_id, $user_badge->user_id))
                        <i class="mr-2 mb-1 material-icons c-accent">favorite</i>
                    @else
                        <i class="mr-2 mb-1 material-symbols-outlined c-accent">favorite</i>
                    @endif
                    <input type="hidden" name="badge_id" value="{{ $user_badge->badge_id }}">
                    <input type="hidden" name="user_id" value="{{ $user_badge->user_id }}">
                </button>
                <p class="m-0 num-supports">{{ $user_badge->num_supports }} </p>

            </div>
        </div>
        <img src={{ asset('storage/badges/' . $user_badge->badge()->badge_id . '.png') }}>
    </div>
@endforeach
</div>
