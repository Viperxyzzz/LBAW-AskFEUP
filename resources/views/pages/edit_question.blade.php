@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-lg-6">
        <div class="mt-5 p-3">
            <h2>Edit question</h2>
        </div>
        <!--include the form-->
        <form method="post" action="{{ route('update_question', array('id' => $question->question_id)) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <div class="card p-3 my-5">
                <div class="form-group">
                  <label class="title-blue" for="title">Title</label>
                  <input id="title" type="text" name="title" class="form-control-lg" value="{{$question->title}}" required/>
                </div>
              </div>
              <div class="card p-3 my-5">
                <div class="form-group">
                  <label class="title-blue" for="full_text">Question</label>
                  <input id="full_text" type="text" name="full_text" class="form-control-lg" value="{{$question->full_text}}" required/>
                </div>
              </div>
            <button class="my-5" type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection
<script>
    //@include('partials.create_question.question_tags')
</script>