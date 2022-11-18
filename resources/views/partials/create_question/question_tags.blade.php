<div class="card p-3">
    <div class="form-group">
        <label class="title-blue" for="tags">Tags</label>
        <small id="tagsHelp" class="form-text text-muted">Add tags to describe what your question is about</small>
        <select class="form-control" id="tags" name="tags[]" multiple size="6">
            @foreach ($tags as $tag)
                <option value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
            @endforeach
        </select>
    </div>
</div>