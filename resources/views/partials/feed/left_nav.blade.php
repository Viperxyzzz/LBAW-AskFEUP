<nav class="col-lg-2 p-5 border-right">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link rounded p-2" aria-current="page" href="{{url('/')}}">
                <i width="16" height="16" class="material-symbols-outlined ">home</i>
                Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded p-2" href="{{ url('/browse') }}">
                <i width="16" height="16" class="material-symbols-outlined ">search</i>
                Questions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded p-2" href="#">
                <i width="16" height="16" class="material-symbols-outlined ">sell</i>
                Tags
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded p-2" href="{{ url('/users') }}">
                <i width="16" height="16" class="material-symbols-outlined ">person</i>
                Users
            </a>
        </li>
    </ul>
</nav>