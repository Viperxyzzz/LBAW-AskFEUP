<div class="card my-5">
    <div class="card-body d-flex justify-content-between">
        <div style="font-size: 2rem">
            <p class="card-text">{{ $answer->full_text }}</p>
        </div>
        <div class="ml-5">
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
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">


 

        <p class="m-0">{{ \Carbon\Carbon::parse($answer->date)->format('d/m/Y')}}</p>
        <p class="m-0">
            <em>by</em>
            <a href="#"> {{ $answer->author->name }}</a>
        </p>
    </div>
    @foreach ($answer->comments()->orderBy('num_votes', 'DESC')->get() as $comment)
                @include('partials.question_page.comment_card', ['comment' => $comment])
    @endforeach

</div>