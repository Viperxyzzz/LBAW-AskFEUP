@if ($answers->isEmpty())
  <p>No answers found!</p>
@endif
@foreach ($answers as $answer)
  <h3>
    <strong>Question:</strong>
    <a href="{{route('question', array('id' => $answer->question()->get()->value('question_id'))) }}">
      {{$answer->question()->get()->value('title')}}
    </a>
  </h3>
  @include('partials.question_page.answer_card', ['answer' => $answer, 'question' => $answer->question()->first()])
@endforeach