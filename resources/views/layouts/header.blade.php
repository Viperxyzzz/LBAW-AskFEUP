<header>
    <div class="d-flex bg-light border-bottom pt-3 pl-3 align-items-start">
        <nav class="d-flex align-items-center">
            <h3><a class="mx-2" href="{{ url('/') }}">AskFeup</a></h3>
            <h5><a class="link-dark mx-2" href="{{ url('/') }}">Home</a></h5>
            <h5><a class="link-dark mx-2" href="#">FAQ</a></h5>
            <h5><a class="link-dark mx-2" href="#">About</a></h5>
        </nav>
        <form method="GET" action="{{ route('browse') }}">
            <input id="searchText" name="searchText" type="text" placeholder="Search..." class="ml-4 col-lg-3"></input>
            <button type="submit" class="rounded">
            <i width="4" height="4" class="material-symbols-outlined ">search</i>
            </button>
        </form>
        <nav class="d-flex align-items-center ml-auto">
            @if (Auth::check())
            <div class="dropdown mr-3">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ Auth::user()->name }}
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ url('/profile') }}">
                        <i width="16" height="16" class="material-symbols-outlined ">person</i>
                        Profile
                    </a>
                    <a class="dropdown-item" href="{{ url('/logout') }}">
                        <i width="16" height="16" class="material-symbols-outlined ">logout</i>
                        Logout
                    </a>
                </div>
            @endif
        </nav>
    </div>
</header>