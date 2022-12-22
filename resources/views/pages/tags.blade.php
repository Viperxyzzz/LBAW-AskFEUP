@extends('layouts.app')
@section('content')
<div class="row">
    @include('partials.tags.tag_filter')
    <div class="col-lg-10 mt-5">
        <h2>Tags</h2>
        <input id="tags-search" name="tags-search" value="" autocomplete="off" class="col-sm-3" type="text" placeholder="Search...">
        <div id="tags-list" class="d-flex flex-wrap">
        @foreach($tags as $tag) 
            <div class="card m-3" style="width: 250px;">
                <div class="card-header d-flex align-items-start justify-content-between">
                    <a href="/browse/?tags[]={{ $tag->tag_id }}" class="badge p-2 m-1">{{ $tag->tag_name }}</a>
                    @if (Auth::user()->follows_tag($tag->tag_id))
                    <button class="unFollow-tag button-clear px-2 pr-3 pb-2 d-flex" id="unFollow-tag-{{ $tag->tag_id }}">
                        <input type="hidden" value="{{ $tag->tag_id }}">
                        <i class="p-0 pt-2 material-symbols-outlined">done</i>
                        <p class="pb-2">Following</p>
                    </button>
                    @else
                    <button class="follow-tag button-clear px-2 pr-3 pb-2 d-flex" id="follow-tag-{{ $tag->tag_id }}">
                        <input type="hidden" value="{{ $tag->tag_id }}">
                        <i class="p-0 pt-2 material-symbols-outlined">add</i>
                        <p class="pb-2">Follow</p>
                    </button>
                    @endif
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