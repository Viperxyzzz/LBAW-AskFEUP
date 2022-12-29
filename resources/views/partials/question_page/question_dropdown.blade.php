    <div class="dropdown">
        <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
            <i class="material-symbols-outlined">more_vert</i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
@can('edit', $question)
            <form method="get" class="m-0" action="{{ route('edit_question', $question->question_id) }}">
                @csrf
                <input type="hidden" name="question_id" value="{{$question->question_id}}">
                <button class="dropdown-item m-0 edit_question" type="submit">
                    <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                    Edit
                </button>
            </form> 
            <button class="dropdown-item m-0" type="button" data-toggle="modal" data-target="#questionModal">
                <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                Delete
            </button>
@endcan
            <button class="dropdown-item m-0" type="button" data-toggle="modal" data-target="#add-report-modal-{{ $question->question_id }}--">
                <i width="16" height="16" class="material-symbols-outlined ">flag</i>
                Report
            </button>
        </div>
    </div>

@include('partials.question_page.delete_question_modal', ['question' => $question])