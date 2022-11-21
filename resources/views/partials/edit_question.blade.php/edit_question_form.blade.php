<div class="card p-3 my-5">
  <div class="form-group">
    <label class="title-blue" for="title">Title</label>
    <small id="title_help" class="form-text text-muted">Be specific and imagine you’re asking a question to another person. </small>
    <input id="title" type="text" name="title" class="form-control-lg" value="{{$question->title}}" required>
  </div>
</div>
<div class="card p-3 my-5">
  <div class="form-group">
    <label class="title-blue" for="full_text">Question</label>
    <small id="full_text_help" class="form-text text-muted">Introduce and expand your doubt. </small>
    <textarea id="full_text" name="full_text" class="form-control-lg" value="{{$question->full_text}}" required></textarea>
  </div>
</div>