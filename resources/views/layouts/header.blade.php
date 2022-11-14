<header>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <h1><a class="navbar-brand h1" href="{{ url('/') }}">AskFeup</a></h1>
            @if (Auth::check())
            <div class="d-flex">
                <a class="button nav-item d-flex" href="{{ url('/logout') }}"> Logout </a>
                <a class="nav-item p-2" href="{{ url('/profile') }}">{{ Auth::user()->name }}</a>
                @endif
            </div>
        </div>
    </nav>
</header>