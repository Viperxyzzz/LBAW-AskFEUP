@extends('layouts.app')
@section('content')
<div class="row">
    @include('partials.tags.tag_filter')
    <div class="col-lg-10 mt-5">
        <div class="row">
            <h2 class="col col-md-9">Tags</h2>
            @can('create', App\Tag::class)
            <button class="col col-md-2 d-flex align-items-center" type="button" data-toggle="modal" data-target="#add-tag-modal">
                <i class="material-symbols-outlined">add</i>
                Create Tag
            </button>
            @endcan
        </div> 
        <input id="tags-search" name="tags-search" value="" autocomplete="off" class="col-sm-3" type="text" placeholder="Search...">
        <ul id="tags-list" class="d-flex flex-wrap">
        @foreach($tags as $tag)
            @include('partials.tags.tag_card', ['tag' => $tag])
        @endforeach
        </ul>
    </div>
</div>
@can('create', App\Tag::class)
<div class="modal fade" id="add-tag-modal" tabindex="-1" aria-labelledby="addTagModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title c-primary" id="addTagModalLabel">Create tag</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="material-symbols-outlined">close</i>
        </button>
      </div>
        <div class="modal-body">
            <h5>Name</h5>
            <input type="text" name="name" required autofocus>
            <h5>Description</h5>
            <input type="text" name="description" required>
            <label class="title-blue" for="topics">Topics</label>
            <select class="form-control" id="topics" name="topic" size="6">
                @foreach ($topics as $topic)
                    <option value="{{ $topic->topic_id }}">{{ $topic->topic_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-footer border-0">
            <button type="button" class="button-outline" data-dismiss="modal">Close</button>
            <button class="button add-tag" data-dismiss="modal" type="submit">
                Confirm
            </button>
        </div>
  </div>
</div>
@endcan


@endsection