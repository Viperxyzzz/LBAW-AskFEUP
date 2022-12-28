<div class="d-flex border p-5 col-md-12">
    <img src="{{asset('storage/'.($user->picture_path).'.jpeg')}}" alt="avatar"
              class="rounded-circle">
    <div class="d-flex justify-content-between w-100">
        <div class='profile-info'>
            @if ($user->is_blocked())
            <a href={{ url('dashboard') }} class='d-flex ml-5 p-2 border border-danger rounded align-items-baseline'>
                <h4 class="m-0 text-danger">
                    <i class="p-0 material-symbols-outlined">warning</i>
                    This user is blocked!
                </h4>
            </a>
            @endif
            <h1 class="m-0 mt-2 ml-5">{{$user->name}}</h1>
            <h4 class="text-secondary ml-5"><em>{{$user->username}}</em></h4>
            <h4 class="ml-5">{{$user->email}}</h4>
        </div>
        <div>
            @can('create', App\Block::class)
                @if ($user->is_blocked())
                @else
                    <button class="block-user button d-flex justify-content-center align-items-center" type="button" 
                        data-toggle="modal" data-target="#add-block-modal" style="width: 14rem">
                        <i class="material-symbols-outlined mr-1">block</i>
                        Block user
                    </button> 
                    
                @endif
            @endcan
            @can('edit', $user)
                <a href="{{ url('/settings', ['id' => $user->user_id]) }}">
                    <button style="width: 14rem">Edit Profile</button>
                </a>
            @endcan
            <h4 class="text-right"><strong class="title-blue">{{$user->score}}</strong> points</h4>
        </div>
    </div>
</div>

@can('create', App\Block::class)
<!-- Create block modal box -->
<div class="modal fade" id="add-block-modal" tabindex="-1" aria-labelledby="blockModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title" id="blockModalLabel">Block this User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="material-symbols-outlined">close</i>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="user_id" value="{{ $user->user_id }}">
        <h5>Reason</h5>
        <input type="text" name="reason" placeholder="Reason for the block" required>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="button-outline" data-dismiss="modal">Close</button>
        <button class="button add-block" data-dismiss="modal" type="submit">
            Confirm
        </button>
    </div>
    </div>
</div>
</div>
@endcan