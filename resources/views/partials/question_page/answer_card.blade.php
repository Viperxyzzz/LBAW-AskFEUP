
<div class="card my-5 answer" id="answer_{{$answer->answer_id}}">
    @include('partials.question_page.delete_answer_modal', ['answer' => $answer])
    <div class="card-body d-flex justify-content-between">
        <div class="flex-fill">
            <p class="m-0">
                <img src="{{asset('storage/'.($answer->author->picture_path).'.jpeg')}}" class="img-fluid rounded-circle" alt="user image" width="25px">
                <a class="font-weight-bold" href="{{url("/users/$answer->user_id")}}"> {{ $answer->author->name }}</a>
            </p>
            <div class="answer-full-text">
                <p class="card-text pb-5 pt-2">{{ $answer->full_text }}</p>
            </div>
        </div>
        <div class="ml-5 d-flex">
            @can('edit', $answer)
                <div class="dropdown">
                    <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
                        <i class="material-symbols-outlined">more_vert</i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <data class="answer_id" hidden>{{ $answer->answer_id }}</data>
                            <button class="dropdown-item edit_answer" type="button">
                                <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                                Edit
                            </button>
                        <input type="hidden" name="answer_id" value="{{$answer->answer_id}}">
                        <button class="dropdown-item" type="button" data-toggle="modal" data-target="#answerModal_{{$answer->answer_id}}">
                            <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                            Delete
                        </button>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-start mt-2">
            <button class="button-clear m-0 px-1" type="button">
                <i width="16" height="16" class="material-symbols-outlined ">arrow_upward</i>
            </button>
            <p class="m-0 px-1 pt-1">{{ $answer->num_votes }}</p>
            <button class="button-clear d-block m-0 px-1" type="button">
                <i width="16" height="16" class="material-symbols-outlined ">arrow_downward</i>
            </button>
            <button class="button-clear m-0 px-1" type="button">
                <i width="12" height="12" class="material-symbols-outlined ">chat_bubble</i>
            </button>
            @if ($answer->is_correct)
                <i class="material-symbols-outlined c-primary b-accent rounded-circle ml-2">
                    check
                </i>
            @endif
            @if($answer->was_edited)
                <p class="m-0 p-0 mt-1 ml-3">edited</p>
            @endif
        </div>
        <p class="m-0">{{ \Carbon\Carbon::parse($answer->date)->format('d/m/Y')}}</p>
    </div>
    <div class="answer-comments">
        @foreach ($answer->comments()->orderBy('num_votes', 'DESC')->get() as $comment)
            @include('partials.question_page.comment_card', ['comment' => $comment])
        @endforeach
    </div>
</div>
