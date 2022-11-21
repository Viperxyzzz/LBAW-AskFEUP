<header>
    <div class="d-flex flex-wrap bg-light border-bottom pt-3 pl-3 align-items-start">
        <nav class="d-flex align-items-center mr-3">
            <h3><a class="mx-2" href="{{ url('/') }}">AskFeup</a></h3>
            <h5><a class="link-dark mx-2" href="{{ url('/') }}">Home</a></h5>
            <h5><a class="link-dark mx-2" href="{{ url('/browse') }}">Browse</a></h5>
            <h5><a class="link-dark mx-2" href="{{ url('/users') }}">Users</a></h5>
        </nav>
        <form class="col-lg-6 m-0 p-0 d-flex align-items-top flex-nowrap" method="GET" action="{{ route('browse') }}">
            <input id="searchText" name="searchText" type="text" placeholder="Search..." class="col-lg-5"></input>
            <button type="submit" class="button button-clear p-2">
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
                    <a class="dropdown-item" href="{{ url('/users/'.Auth::id()) }}">
                        <i width="16" height="16" class="material-symbols-outlined ">person</i>
                        Profile
                    </a>
                    <a class="dropdown-item" href="{{ url('/logout') }}">
                        <i width="16" height="16" class="material-symbols-outlined ">logout</i>
                        Logout
                    </a>
                </div>
            @else
                <a class="button button-outline" href="{{ route('login') }}">Login</a>
                <a class="button mx-2" href="{{ route('register') }}">Register</a>
            @endif
        </nav>
    </div>
</header>