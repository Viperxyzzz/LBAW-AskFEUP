<form id="edit-user-form" action="{{ route('update_user_api') }}" method="POST">
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
            <input type="text" name="name" value="{{$user->name}}">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="{{$user->username}}">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{$user->email}}">
        </div>

        <div id="edit-pass-div"></div>
        @error('new_password')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <div id="edit-pass-div-conf"></div>
    </div>
</form>
<button class="ml-5" id="edit-pass" onclick="editPass()">Edit Password</button>
<button class="ml-5" id="save-settings" onclick="submitSettings()">Save</button>
<a class="ml-5" id="cancel-settings" href="/profile"><button class="btn-danger">Cancel</button></a>