<div class="modal fade" id="commentModal_{{$comment->comment_id}}" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="commentModalLabel">Delete comment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="material-symbols-outlined">close</i>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this comment?
        </div>
        <div class="modal-footer border-0">
          <input type="hidden" name="comment_id" value="{{$comment->comment_id}}">
          <button type="button" class="button-outline" data-dismiss="modal">Close</button>
          <button type="button" class="button delete-comment" data-dismiss="modal">Confirm</button>
        </div>
      </div>
    </div>
  </div>