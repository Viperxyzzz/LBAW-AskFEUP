<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="questionModalLabel">Delete question</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="material-symbols-outlined">close</i>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this question?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="button-outline" data-dismiss="modal">Close</button>
        <form method="post" class="m-0" action="{{ route('question_delete_api', $question->question_id) }}">
            @method('delete')
            @csrf
            <input type="hidden" name="question_id" value="{{$question->question_id}}">
            <button class="button" type="submit">
                Confirm
            </button>
        </form> 
      </div>
    </div>
  </div>
</div>
