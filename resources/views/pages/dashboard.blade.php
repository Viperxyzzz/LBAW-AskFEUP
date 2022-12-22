@extends('layouts.app')

@section('content')
<div class="row">
    @include('partials.feed.left_nav')
    <div class="col-lg-10 mt-5">
        <h2 class="m-3">Moderator Dashboard</h2>
        <hr>
        <h3 class="m-4">Reports</h3>
        <div class="d-flex flex-wrap">
            @each('partials.admin.report_card', $reports, 'report')
        </div>

    </div>
</div>
@endsection