<div class="card my-5" style="width: 90%;">
    <div class="card-body d-flex justify-content-between">
        <div>
            <p class="card-text">{{ $comment->full_text }}</p>
        </div>
        <div class="ml-5">
            <aside class="question-stats">
                <p class="m-0 text-nowrap">{{ $comment->num_votes }} votes</p>
            </aside>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <p class="m-0">{{ \Carbon\Carbon::parse($comment->date)->format('d/m/Y')}}</p>
        <p class="m-0">
            <em>by</em>
            <a href="#"> {{ $comment->author->name }}</a>
        </p>
    </div>

</div>