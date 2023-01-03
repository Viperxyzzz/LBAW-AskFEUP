<nav class="col-lg-3 border-right">
    <ul class="nav nav-pills flex-column align-items-start">
        <li class="nav-item">
            <a class="nav-link active profile-nav" id="user-overview">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link profile-nav" id="user-questions">Questions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link profile-nav" id="user-answers">Answers</a>
        </li>
        @if (Auth::id() === $user->user_id)
        <li class="nav-item ">
            <a class="nav-link profile-nav" id="user-tags">Following Tags</a>
        </li>
        @endif
    </ul>
</nav>
