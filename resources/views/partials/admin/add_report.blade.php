<div class="modal fade" 
    id="add-report-modal-{{ $question->question_id }}-{{ $answer->answer_id ?? '' }}-{{ $comment->comment_id ?? '' }}"
    tabindex="-1" aria-labelledby="addReportLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="answerModalLabel">Report this content</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-symbols-outlined">close</i>
            </button>
        </div>
        <div class="modal-body">
                <h5>Reason</h5>
                <input type='text' name='reason'>
                <input type='hidden' name='question_id' value="{{ $question->question_id }}">
                <input type='hidden' name='answer_id' value="{{ $answer->answer_id ?? '' }}">
                <input type='hidden' name='comment_id' value="{{ $comment->comment_id ?? '' }}">
        </div>
        <div class="modal-footer border-0">
            <button type="button" class="button-outline" data-dismiss="modal">Close</button>
            <button type="button" class="button add-report" data-dismiss="modal">Confirm</button>
        </div>
    </div>
  </div>
</div>
