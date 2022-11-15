<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href='https://css.gg/check-o.css' rel='stylesheet'>
<div class="card my-5">
    <div class="card-body d-flex justify-content-between">
        <div>
            <p class="card-text">{{ $answer->full_text }}</p>
        </div>
        <div class="ml-5">
            <aside class="question-stats">
                @if ($answer->is_correct == 1)
                <i class="gg-check-o" style="color: green"></i>
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
    @foreach ($answer->comments()->orderBy('comment_id')->get() as $comment)
                @include('partials.question_page.comment_card', ['comment' => $comment])
        @endforeach

</div>