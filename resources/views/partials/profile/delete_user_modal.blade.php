<div class="modal fade" id="userModal_{{$user->user_id}}" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="userModalLabel">Delete account</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="material-symbols-outlined">close</i>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete your account?
        </div>
        <div class="modal-footer border-0">
          <input type="hidden" name="user_id" value="{{$user->user_id}}">
          <button type="button" class="button-outline" data-dismiss="modal">Close</button>
          <button type="button" class="button delete-user" data-dismiss="modal">Confirm</button>
        </div>
      </div>
    </div>
  </div>
