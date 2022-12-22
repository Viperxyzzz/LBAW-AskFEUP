
<div id="report_{{ $report->report_id }}" class="card m-3" style="width: 350px;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="pb-2">
            <a href="{{ $report->url() }}" class="m-0 d-flex">Report #{{ $report->report_id }}
                <p class="c-secondary m-0 ml-2">{{ $report->type() }}</p>
            </a>
        </h5>
        <div class="dropdown">
            <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
                <i class="material-symbols-outlined">more_vert</i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <data class="report_id" hidden>{{ $report->report_id }}</data>
                    <button class="dropdown-item delete-report m-0" type="button">
                        <i width="16" height="16" class="material-symbols-outlined ">done</i>
                        Dismiss
                    </button>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <h4>{{ $report->reason }}</h4>
        <p>{{ $report->content() }}</p>

    </div>
    <div class="card-footer d-flex p-4 justify-content-between">
        <p class="m-0">
            <img src="{{asset('storage/'.($report->author()->picture_path).'.jpeg')}}" class="img-fluid rounded-circle" alt="user image" width="25px">
            <a href="{{url("/users/" . strval($report->author()->user_id))}}"> {{ $report->author()->name }}</a>
        </p>
        <p class="m-0">{{ $report->date }}</p>
    </div>
</div>
