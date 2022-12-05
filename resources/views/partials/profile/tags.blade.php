<div id="tags-list" class="d-flex flex-wrap">
@foreach($tags as $tag) 
    <div class="card m-3" style="width: 250px;">
        <div class="card-header d-flex justify-content-between">
            <p class="badge p-2 m-1">{{ $tag->tag_name }}</p>
            <a href="#" class="p-0">
                <i class="p-0 material-symbols-outlined">done</i>
                Following
            </a>
        </div>
        <div class="card-body">
            <p>{{ $tag->tag_description }}</p>
        </div>
    </div>
@endforeach
</div>