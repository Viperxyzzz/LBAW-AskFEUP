@extends('layouts.app')
@section('content')
<div class="row">
    @include('partials.feed.left_nav')
    <div class="col-lg-10 mt-5">
        <h2>Tags</h2>
        <input id="tags-search" name="tags-search" value="" autocomplete="off" class="col-sm-3" type="text" placeholder="Search...">
        <div id="tags-list" class="d-flex flex-wrap">
        @foreach($tags as $tag) 
            <div class="card m-3" style="width: 250px;">
                <div class="card-header d-flex justify-content-between">
                    <p class="badge p-2 m-1">{{ $tag->tag_name }}</p>
                    <a href="#" class="p-0">
                        <i class="p-0 material-symbols-outlined">add</i>
                        Follow
                    </a>
                </div>
                <div class="card-body">
                    <p>{{ $tag->tag_description }}</p>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
@endsection