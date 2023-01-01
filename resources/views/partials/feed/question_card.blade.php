<div class="card my-5">
    <div class="card-body d-flex justify-content-between">
        <div>
            <a
            class="card-title font-weight-bold" 
            href="{{ route('question', array('id' => $question->question_id)) }}">
            {{ $question->title }}
            </a>
            <p class="card-text">{{ $question->full_text }}</p>
            <div class="flex">
                @foreach($question->tags()->orderBy('tag_id')->get() as $tag)
                    <span class="badge p-2">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
        </div>
        <div class="ml-5">
            <aside class="question-stats">
                <p class="m-0 text-nowrap">{{ $question->num_votes }} votes</p>
                <p class="m-0 text-nowrap">{{ $question->num_views }} views</p>
                <p class="m-0 text-nowrap">{{ $question->num_answers }} answers</p>
            </aside>
        </div>
        @include('partials.question_page.question_dropdown', ['user' => Auth::user()])
    </div>
    <div class="card-footer d-flex justify-content-between">
        <p class="m-0">{{ $question->date_distance() }}</p>
        <p class="m-0">
            <em>by</em>
            @if($question->is_accessible_user())
            <a href="{{ url("/users/$question->author_id") }}"> {{ $question->author->name }}</a>
            @else
            <a> {{ $question->author->name }}</a>
            @endif
        </p>
    </div>
    @include('partials.admin.add_report', $question)
</div>