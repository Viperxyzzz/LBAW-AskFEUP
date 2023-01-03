@extends('layouts.app')

@section('content')

@include('partials.messages.feedback')

<div class="row justify-content-md-center">
    <div class="col-lg-6">
        <div class="mt-5 p-3">
            <h2>Edit question</h2>
        </div>
        <!--include the form-->
        <form id="edit-question-form" method="post" action="{{ route('update_question', array('id' => $question->question_id)) }}" enctype="multipart/form-data">
            @method('PUT')
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
              <div class="card p-3">
                <div class="form-group">
                    <label class="title-blue" for="tags">Tags</label>
                    <select class="form-control" id="tags" name="tags[]" multiple size="6">
                        @foreach ($tags as $tag)
                            @if ($question->tags->contains($tag->tag_id))
                              <option value="{{ $tag->tag_id }}" selected>{{ $tag->tag_name }}</option>
                            @else
                              <option value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
          </form>
          <div class="row justify-content-between">
              <a method="get" href="{{ route('question', array('id' => $question->question_id)) }}">
                  <button class=" button-clear my-5 d-flex">
                      <p class="pb-2">Cancel</p>
                  </button>
              </a>
              <button class="my-5" type="submit" class="btn btn-primary" onclick="submitQuestionUpdate()">Submit</button>
          </div>
      </div>
  </div>
@endsection
