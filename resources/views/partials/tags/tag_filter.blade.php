<div class="col-lg-2 p-5 border-right">
    <h3>Topics</h3>
    <div class="mt-3">
            @foreach ($topics as $topic)
                <label class="multi-check">
                    <input type="checkbox" class="topic-check" value="{{ $topic->topic_id }}"></input>
                    {{ $topic->topic_name }}
                </label>
            @endforeach
    </div>
</div>