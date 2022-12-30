<form id="edit-user-form" action="{{ route('update_user_api', ['id' => $user->user_id]) }}" method="POST">
    @csrf
    <div class="card m-5 p-5">
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

        <div id="edit-pass-div"></div>
        <div id="edit-pass-div-conf"></div>
    </div>
</form>
<button class="ml-5" id="edit-pass" onclick="editPass()">Edit Password</button>
<button class="ml-5" id="save-settings" onclick="submitSettings()">Save</button>
<a class="ml-5" id="cancel-settings" href="{{ route('users', array('id' => $user->user_id)) }}"><button class="btn-danger">Cancel</button></a>