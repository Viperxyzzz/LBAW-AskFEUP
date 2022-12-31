<div id="tags-list" class="d-flex flex-wrap">
@foreach($tags as $tag) 
    <div class="card m-3" style="width: 250px;">
        <div class="card-header d-flex align-items-start justify-content-between">
            <p class="badge p-3 m-1">{{ $tag->tag_name }}</p>
            <button class="unFollow-tag button-clear px-2 pr-3 pb-2 d-flex" id="unFollow-tag-{{ $tag->tag_id }}">
                <input type="hidden" value="{{ $tag->tag_id }}">
                <i class="p-0 pt-2 material-symbols-outlined">done</i>
                <p class="pb-2">Following</p>
            </button>
        </div>
        <div class="card-body">
            <p>{{ $tag->tag_description }}</p>
        </div>
    </div>
@endforeach
</div>