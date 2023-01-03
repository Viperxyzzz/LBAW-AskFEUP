@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form id="edit-user-form" action="{{ route('update_user_api', ['id' => $user->user_id]) }}" method="POST" enctype="multipart/form-data">>
    @method('PUT')
    @csrf
    <div class="card m-5 p-5">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="edit-full-name" name="name" value="{{$user->name}}">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" id="edit-username" name="username" value="{{$user->username}}">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" id="edit-email" name="email" value="{{$user->email}}">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" id="edit-picture-path" name="picture_path">
        </div>

        <div id="edit-pass-div"></div>
        <div id="edit-pass-div-conf"></div>
    </div>
</form>
<button class="ml-5" id="edit-pass" onclick="editPass()">Edit Password</button>
<button class="ml-5" id="save-settings" onclick="submitSettings()">Save</button>
<a class="ml-5" id="cancel-settings" href="{{ route('users', array('id' => $user->user_id)) }}"><button class="btn-danger">Cancel</button></a>