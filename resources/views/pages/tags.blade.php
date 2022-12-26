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
        <div id="tags-list" class="d-flex flex-wrap">
        @foreach($tags as $tag) 
            <div class="card m-3" style="width: 250px;">
                <div class="card-header d-flex align-items-start justify-content-between">
                    <p class="badge p-3 m-1 mt-2">{{ $tag->tag_name }}</p>
                    <div class="d-flex justify-content-end">
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

                        <div class="dropdown">
                            <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
                                <i class="material-symbols-outlined">more_vert</i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <input type="hidden" name="question_id" value="{{$tag->tag_id}}">
                                <button class="dropdown-item edit-tag">
                                    <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                                    Edit
                                </button>
                                <button class="dropdown-item" type="button" data-toggle="modal" data-target="#remove-tag-modal-{{ $tag->tag_id }}">
                                    <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>{{ $tag->tag_description }}</p>
                </div>
            </div>
            
            @can('manage', App\Tag::class)
            <!-- Delete tag modal box -->
            <div class="modal fade" id="remove-tag-modal-{{ $tag->tag_id }}" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="questionModalLabel">Delete tag</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="material-symbols-outlined">close</i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this tag?</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="button-outline" data-dismiss="modal">Close</button>
                    <form method="GET" class="m-0" action="{{ url("api/tag/delete/{$tag->tag_id}") }}">
                        @csrf
                        <button class="button" type="submit">
                            Confirm
                        </button>
                    </form>
                </div>
                </div>
            </div>
            </div>
            @endcan

        @endforeach
        </div>
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

      <form method="POST" class="m-0" action="{{ route('tag_create_api') }}">
        @method('post')
        @csrf
        <div class="modal-body">
            <h5>Name</h5>
            <input type="text" name="name" required>
            <h5>Description</h5>
            <input type="text" name="description" required>
            <label class="title-blue" for="topics">Topics</label>
            <select class="form-control" id="topics" name="topic" size="6" required>
                @foreach ($topics as $topic)
                    <option value="{{ $topic->topic_id }}">{{ $topic->topic_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-footer border-0">
            <button type="button" class="button-outline" data-dismiss="modal">Close</button>
            <button class="button" type="submit">
                Confirm
            </button>
        </form> 
      </div>
  </div>
</div>
@endcan


@endsection