<div class="card my-5">
    <div class="card-body d-flex justify-content-between">
        <div>
            <p class="card-text">{{ $answer->full_text }}</p>
        </div>
        <div class="ml-5">
            <aside class="question-stats">
                <p class="m-0 text-nowrap">{{ $answer->num_votes }} votes</p>
                <p class="m-0 text-nowrap">{{ $answer->is_correct }}</p>
                <p class="m-0 text-nowrap">{{ $answer->was_edited }}</p>
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
    @foreach ($answer->comments()->orderBy('comment_id')->get() as $comment)
                @include('partials.question_page.comment_card', ['comment' => $comment])
        @endforeach

</div>