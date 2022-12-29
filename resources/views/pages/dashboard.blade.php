@extends('layouts.app')

@section('content')
<div class="row">
    @include('partials.admin.left_nav')

    <div class="col-lg-10 mt-5">
        <h2 class="m-3">Moderator Dashboard</h2>
        <hr>
        <div id="dashboard-reports-tab" class="dashboard-tab tab-open">
            <h3 class="m-4">Reports</h3>
            @if($reports->isEmpty())
                <p class="m-4">There are no new reports!</p>
            @endif
            <div class="d-flex flex-wrap">
                @each('partials.admin.report_card', $reports, 'report')

            </div>
        </div>
        <div id="dashboard-blocks-tab" class="dashboard-tab">
            <h3 class="m-4">Blocked Users</h3>
            @if($blocks->isEmpty())
                <p class="m-4">There are no blocked users!</p>
            @endif
            <div class="d-flex flex-wrap">
                @include('partials.admin.blocks', $blocks)

            </div>
        </div>
    </div>

</div>
@endsection