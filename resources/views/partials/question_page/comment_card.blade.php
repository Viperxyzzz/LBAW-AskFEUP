<div style="border: 1px rgba(0,0,0,.125); border-style: solid none none;">
    <div class="card-body d-flex justify-content-between">
        <div>
            <p class="card-text">{{ $comment->full_text }}</p>
        </div>
        <div class="ml-5" style="font-size: 1.3rem">
            <aside class="question-stats">
                <p class="m-0 text-nowrap">{{ $comment->num_votes }} votes</p>
            </aside>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between" style="background-color: white; padding: 0.1">
        <p class="m-0" style="font-size: 1.2rem">{{ \Carbon\Carbon::parse($comment->date)->format('d/m/Y')}}</p>
        <p class="m-0" style="font-size: 1.2rem; margin: 0">
            <em>by</em>
            <a href="#"> {{ $comment->author->name }}</a>
        </p>
    </div>

</div>