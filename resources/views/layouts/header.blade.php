<header>
    <div class="d-flex flex-wrap bg-light border-bottom pt-3 pl-3 align-items-start">
        <nav class="d-flex align-items-center mr-3">
            <h3><a class="mx-2" href="{{ url('/') }}">AskFeup</a></h3>
            <h5><a class="link-dark mx-2" href="{{ url('/') }}">Home</a></h5>
            <h5><a class="link-dark mx-2" href="{{ url('/browse') }}">Browse</a></h5>
            <h5><a class="link-dark mx-2" href="{{ url('/tags') }}">Tags</a></h5>
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
                @if (Auth::user()->is_mod())
                    <a href="{{ url('/dashboard') }}" class='d-flex mr-3 p-3 button button-outline align-items-center '>
                        <i class="material-symbols-outlined p-2">problem</i>
                        Dashboard
                    </a>
                @endif

                <div class="dropdown z-index-master mr-3 ml-2">
                    <button class="btn bg-transparent m-0 p-0 border-0 d-flex shadow-none" id="btn-notifications" type="button" data-toggle="dropdown" aria-expanded="true">
                        <div class="d-flex align-items-start">
                            @if($num = Auth::user()->num_non_viewed_notifications())
                                <span class="badge rounded-pill" id="num-notifications">
                                    {{$num}}
                                </span>
                            @endif    
                        </div>
                        <span class="mt-2 material-symbols-outlined">Notifications</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <ul>
                            @foreach(Auth::user()->notifications()->orderBy('date', 'DESC')->get() as $notification)
                                <li class="dropdown-item">
                                    <button class="btn bg-transparent shadow-none border-0 d-flex justify-content-between align-items-center w-100 button-notification" id="button-notification-{{$notification['notification_id']}}" aria-expanded="true" onclick="redirect_notification({{$notification['notification_id']}})">
                                        <div class="d-flex flex-column">
                                            <strong class="h5 text-left">{{$notification["notification_text"]}}</strong>
                                            <small class="text-left">{{date("d/m/Y H:i:s", strtotime($notification["date"]))}}</small>
                                        </div>
                                        @if(!$notification["viewed"])
                                            <span class="material-icons ml-4 red-circle-notification">circle</span>
                                        @endif
                                    </button>
                                </li>
                                <hr>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="dropdown mr-3">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
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
            </div>
        </nav>
    </div>
</header>