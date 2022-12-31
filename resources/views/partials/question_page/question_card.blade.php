<div>
    <div class="d-flex">
        <div>
            <form method="POST" action="{{ route('vote_question', $question->question_id) }}">
                {{ csrf_field() }}
                <input type="hidden" name="vote" value="1"></input>
                <input type="hidden" name="question_id" value="{{$question->question_id}}"></input>
                <button class="button-clear p-1 mr-2" type="submit">
                    <i width="16" height="16" class="material-symbols-outlined ">keyboard_arrow_up</i>
                </button>
            </form>
            <form method="POST" action="{{ route('vote_question', $question->question_id) }}">
                {{ csrf_field() }}
                <input type="hidden" name="vote" value="-1"></input>
                <input type="hidden" name="question_id" value="{{$question->question_id}}"></input>
                <button class="button-clear p-1 mr-2" type="submit">
                    <i width="16" height="16" class="material-symbols-outlined ">keyboard_arrow_down</i>
                </button>
            </form>
        </div>
        <div class="d-flex flex-fill justify-content-between">
            <div>
                <h2>{{$question->title}}</h2>
                <p>{{$question->full_text}}</p>
            </div>
            @include('partials.question_page.question_dropdown')
        </div>
    </div>
    <div class="d-flex justify-content-between">
        <div>
        @foreach($question->tags()->orderBy('tag_id')->get() as $tag)
            <span class="badge p-2">{{ $tag->tag_name }}</span>
        @endforeach
        </div>
        <div class="d-flex">
            <p class="m-2"><strong class="title-blue">{{$question->num_votes}}</strong> votes</p>
            <p class="m-2"><strong class="title-blue">{{$question->num_views}}</strong> views</p>
            <p class="m-2"><strong class="title-blue">{{$question->num_answers}}</strong> answers</p>
        </div>
    </div>
    <hr class="mx-0 my-2">
    <div class=" d-flex mx-2 justify-content-between">
        <div class="row">
            <button class="add-comment-question-form-button button button-clear m-0 px-1" type="button">
            <i width="12" height="12" class="material-symbols-outlined ">chat_bubble</i>
            </button>
            <p class="m-0">
                {{ $question->date_distance() }}
                @if ($question->was_edited)
                    (Edited)
                @endif
            </p>
        </div>
        <div class="row justify-self-end">
            <p class="m-0">
                <em>by</em>
                <a href="{{ url("/users/$question->author_id") }}"> {{ $question->author->name }}</a>
            </p>
        </div>
    </div>
    @include('partials.admin.add_report', $question)
</div>
