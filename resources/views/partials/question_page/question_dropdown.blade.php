@if (Auth::user()->user_id === $question->author_id)
<div class="dropdown ml-auto p-2">
    <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
        <i class="material-symbols-outlined">more_vert</i>
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        <button class="dropdown-item edit_answer">
            <i width="16" height="16" class="material-symbols-outlined ">edit</i>
            Edit
        </button>
        <form method="post" action="{{ route('question_delete_api', $question->question_id) }}">
            @method('delete')
            @csrf
            <input type="hidden" name="question_id" value="{{$question->question_id}}">
            <button class="dropdown-item" type="submit">
                <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                Delete
            </button>
        </form> 
    </div>
</div>
@endif