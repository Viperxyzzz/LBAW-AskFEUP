<nav class="col-lg-2 mt-5 pl-5 ">
    <h3>Order by</h3>
    <form class="ml-2">
        <div class="d-flex m-2 justify-content-center">
            <input type="radio" name="direction" id="desc" checked>
            <label for="desc">
                <i width="13" height="13" class="material-symbols-outlined ">arrow_downward</i>
            </label>
            <input type="radio" name="direction" id="asc">
            <label for="asc">
                <i width="13" height="13" class="material-symbols-outlined ">arrow_upward</i>
            </label>
        </div>
        <input type="radio" name="order" id="date" checked>
        <label for="date">
            <i width="16" height="16" class="material-symbols-outlined ">calendar_today</i>
            Date
        </label>
        <input type="radio" name="order" id="num_votes">
        <label for="num_votes">
            <i width="16" height="16" class="material-symbols-outlined ">done_all</i>
            Score
        </label>
        <input type="radio" name="order" id="num_views">
        <label for="num_views">
            <i width="16" height="16" class="material-symbols-outlined ">trending_up</i>
            Views
        </label>
        <input type="radio" name="order" id="num_answers">
        <label for="num_answers">
            <i width="16" height="16" class="material-symbols-outlined ">mode_comment</i>
            Answers
        </label>
    </form>
    <hr>
    <h3>Filter</h3>
    <div>
        <h6>Tags</h6>
        <input type="text" placeholder="Search for tags...">
    </div>
</nav>