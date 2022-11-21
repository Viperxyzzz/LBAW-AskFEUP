@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@elseif (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif
<div class="card my-5 answer" id="answer_{{$answer->answer_id}}">
    <strong class="ml-4 mb-0">Answer:</strong>
    <div class="card-body d-flex justify-content-between">
        <div style="font-size: 2rem" class="answer-full-text">
            <p class="card-text">{{ $answer->full_text }}</p>
        </div>
        <div class="ml-5 d-flex">
            <aside class="question-stats">
                @if ($answer->is_correct == 1)
                <span class="material-symbols-outlined" style="color: green">
                    task_alt
                </span>
                @endif
                <p class="m-0 text-nowrap">{{ $answer->num_votes }} votes</p>
                @if($answer->was_edited == 1)
                <p class="m-0 text-nowrap">edited</p>
                @endif
            </aside>
            @if (Auth::check())
                @if (Auth::user()->user_id === $answer->user_id)
                <div class="dropdown">
                    <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
                        <i class="material-symbols-outlined">more_vert</i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <data class="answer_id" hidden>{{ $answer->answer_id }}</data>
                            <input type="hidden" name="answer_id" value="{{$answer->answer_id}}">
                            <button class="dropdown-item edit_answer" type="button">
                                <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                                Edit
                            </button>
                        <form method="POST" action="{{ route('answer_delete_api', $answer->answer_id) }}">
                            @method('delete')
                            @csrf
                            <input type="hidden" name="answer_id" value="{{$answer->answer_id}}">
                            <button class="dropdown-item" type="submit">
                                <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <p class="m-0">{{ \Carbon\Carbon::parse($answer->date)->format('d/m/Y')}}</p>
        <p class="m-0">
            <em>by</em>
            <a href="{{url("/users/$answer->user_id")}}"> {{ $answer->author->name }}</a>
        </p>
    </div>
    <div class="answer-comments">
        @foreach ($answer->comments()->orderBy('num_votes', 'DESC')->get() as $comment)
            <strong class="ml-4 mt-2">Comment:</strong>
            @include('partials.question_page.comment_card', ['comment' => $comment])
        @endforeach
    </div>
</div>
