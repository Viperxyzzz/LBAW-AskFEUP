<div class="modal fade" id="answerModal_{{$answer->answer_id}}" tabindex="-1" aria-labelledby="answerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="answerModalLabel">Delete answer</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="material-symbols-outlined">close</i>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this answer?
      </div>
      <div class="modal-footer border-0">
        <input type="hidden" name="answer_id" value="{{$answer->answer_id}}">
        <button type="button" class="button-outline" data-dismiss="modal">Close</button>
        <button type="button" class="button delete-answer" data-dismiss="modal">Confirm</button>
      </div>
    </div>
  </div>
</div>
