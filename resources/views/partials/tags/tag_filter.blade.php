<div class="col-lg-2 p-5 border-right">
    <h3>Topics</h3>
    <div class="mt-3 d-flex flex-wrap">
            @foreach ($topics as $topic)
                <label class="multi-check filter-option p-2 px-3 m-1">
                    <input type="checkbox" display="none" class="topic-check" value="{{ $topic->topic_id }}"></input>
                    {{ $topic->topic_name }}
                </label>
            @endforeach
    </div>
</div>