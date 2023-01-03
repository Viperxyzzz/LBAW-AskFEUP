<div class="d-flex flex-wrap border p-5 col-md-12">
    @include('partials.profile.delete_user_modal', ['user' => $user])
    <img src="{{asset('storage/'.($user->picture_path).'.jpeg')}}" alt="avatar"
              class="rounded-circle">
    <div class="d-flex justify-content-between" style="flex-grow: 1">
        <div class='profile-info'>
            @if ($user->is_blocked())
            <a href={{ url('dashboard') }} class='warning-blocked d-flex ml-5 p-2 border border-danger rounded align-items-baseline'>
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
        @can('edit', $user)
        <div class="text-right">
            <div class="dropdown">
                <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
                    <i class="material-symbols-outlined">more_vert</i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    @can('create', App\Block::class)
                        <button class="unblock-user dropdown-item justify-content-center align-items-center
                        {{ ($user->is_blocked()) ? ' d-flex ' : 'tab-closed' }}" type="button"
                            style="width: 14rem">
                            <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                            <i class="material-symbols-outlined mr-1">emergency_home</i>
                            Unblock user
                        </button> 
                        @if(Auth::id() !== $user->user_id)
                        <button class="block-user dropdown-item justify-content-center align-items-center
                        {{ ($user->is_blocked()) ? 'tab-closed' : ' d-flex ' }}" type="button" 
                            data-toggle="modal" data-target="#add-block-modal"
                            style="width: 14rem">
                            <i class="material-symbols-outlined mr-1">block</i>
                            Block user
                        </button> 
                        @endif
                    @endcan
                        <a class="dropdown-item" href="{{ url('/settings', ['id' => $user->user_id]) }}">
                            <i class="material-symbols-outlined mr-1">edit</i>
                            Edit Profile
                        </a>
                        <button class="dropdown-item m-0" type="button" data-toggle="modal" data-target="#userModal_{{$user->user_id}}">
                            <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                            Delete
                        </button>
                </div>
                @endcan
            </div>
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