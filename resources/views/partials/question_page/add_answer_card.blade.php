<div class="card mt-5" id="add-answer-card">
    <form method="POST" class="card-body m-0 p-0">
        {{ csrf_field() }}
        <input type="hidden" name="question_id" id="question_id" value="{{ $question_id }}"></input>

        <textarea class="w-100 h-100 m-0 border-0" placeholder="Type something..." rows="5"
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