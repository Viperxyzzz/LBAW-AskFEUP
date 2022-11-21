<nav class="col-lg-2 p-5 border-right">
    <h3>Order by</h3>
    <form class="ml-2 mt-4">
        <div class="d-flex m-2 justify-content-center">
            <input type="radio" class="direction" name="direction-users" id="desc">
            <label for="desc">
                <i width="13" height="13" class="material-symbols-outlined ">arrow_downward</i>
            </label>
            <input type="radio" class="direction" name="direction-users" id="asc" checked>
            <label for="asc">
                <i width="13" height="13" class="material-symbols-outlined ">arrow_upward</i>
            </label>
        </div>
        <input type="radio" name="order-users" id="username" checked>
        <label for="username">
            <i width="16" height="16" class="material-symbols-outlined ">sort_by_alpha</i>
            Alphabetically
        </label>
        <input type="radio" name="order-users" id="score">
        <label for="score">
            <i width="16" height="16" class="material-symbols-outlined ">trending_up</i>
            Score
        </label>
    </form>
</nav>