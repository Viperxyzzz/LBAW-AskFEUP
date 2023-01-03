<div class="row justify-content-center">
    <ul class="d-flex list-unstyled p-3 justify-content-around w-100">
        <li>
            <div class="d-flex flex-column align-items-center">
                <h3>Questions</h3>
                <h3 class="c-primary">{{$user->get_n_asked()}}</h3>
            </div>
        </li>
        <li>
            <div class="d-flex flex-column align-items-center">
            <h3>Answers</h3>
            <h3 class="c-primary">{{ $user->get_n_answered() }}</h3>
            </div>
        </li>
        <li>
            <div class="d-flex flex-column align-items-center">
            <h3>Badges</h3>
            <h3 class="c-primary">{{$user->get_n_badges()}}</h3>
            </div>
        </li>
        <li>
            <div class="d-flex flex-column align-items-center">
            <h3>Tags</h3>
            <h3 class="c-primary">{{$user->get_n_ftags()}}</h3>
            </div>
        </li>
    </ul>
</div>

<div class="row">
    <ul class="d-flex list-unstyled">
        <div class="col-lg-8 ml-4">
            <li>
            <div class="d-flex flex-column">
                <h3>Last 3 asked questions</h3>
                <ul class="d-flex flex-column list-unstyled ml-0">
                @foreach($user->get_last3_asked() as $title)
                    <li>
                        {{ $title }}
                    </li>
                @endforeach
                </ul>
            </div>
            </li>
        </div>

        <div class="col-lg-6">
            <li>
            <div class="d-flex flex-column">
                <h3>Last 3 badges achieved</h3>
                <ul class="d-flex flex-column list-unstyled">
                @foreach($user->get_last3_badges() as $badge)
                    <li class="d-flex">
                    <span class="material-symbols-outlined">military_tech</span>
                    {{$badge}}
                    </li>
                @endforeach
                </ul>
            </div>
            </li>
        </div>
    </ul>
</div>
