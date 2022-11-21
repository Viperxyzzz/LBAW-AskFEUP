<div class="card mt-5">
    <form method="GET" class="card-body m-0 p-0">
        {{ csrf_field() }}
        <textarea class="w-100 h-100 m-0 border-0" value="{{$answer->full_text}}" rows="6"
            id="answer" name="answer" value="{{ old('answer') }}" required></textarea>
        @if ($errors->has('answer'))
            <span class="error">
            {{ $errors->first('answer') }}
            </span>
        @endif
    </form>
    <div class="card-footer text-right">
        <button id="add-answer-button" type="submit" class="m-0">
            Answer
        </button>
    </div>
</div>
