<nav class="col-lg-2 p-5 border-right">
    <h3>Order by</h3>
    <form class="ml-2 mt-4">
        <div class="d-flex m-2 justify-content-center">
            <input type="radio" class="direction" name="direction-questions" id="desc" checked>
            <label for="desc">
                <i width="13" height="13" class="material-symbols-outlined ">arrow_downward</i>
            </label>
            <input type="radio" class="direction" name="direction-questions" id="asc">
            <label for="asc">
                <i width="13" height="13" class="material-symbols-outlined ">arrow_upward</i>
            </label>
        </div>
        <input type="radio" name="order-questions" id="date" checked>
        <label for="date">
            <i width="16" height="16" class="material-symbols-outlined ">calendar_today</i>
            Date
        </label>
        <input type="radio" name="order-questions" id="num_votes">
        <label for="num_votes">
            <i width="16" height="16" class="material-symbols-outlined ">done_all</i>
            Score
        </label>
        <input type="radio" name="order-questions" id="num_views">
        <label for="num_views">
            <i width="16" height="16" class="material-symbols-outlined ">trending_up</i>
            Views
        </label>
        <input type="radio" name="order-questions" id="num_answers">
        <label for="num_answers">
            <i width="16" height="16" class="material-symbols-outlined ">mode_comment</i>
            Answers
        </label>
    </form>
    <hr>
    <h3>Tags</h3>
    <select class="form-control" id="tags" name="tags[]" multiple size="6">
        @foreach ($tags as $tag)
            <option class="tag-filter" value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
        @endforeach
    </select>
</nav>