@if ($questions->isEmpty())
  <p>No questions found!</p>
@endif
@foreach ($questions as $question)
    @include('partials.feed.question_card', ['question' => $question])
@endforeach