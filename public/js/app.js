function addEventListeners() {
  let answerCreator = document.querySelector('#add-answer-button');
  if (answerCreator != null)
    answerCreator.addEventListener('click', sendCreateAnswerRequest);

  let orderRadio = document.querySelectorAll('input[name=order]');
  if (orderRadio != null) {
    orderRadio.forEach(orderButton => {
      orderButton.addEventListener('change', sendOrderQuestionsRequest);
    });

  }
}

function encodeForAjax(data) {
  if (data == null) return null;
  return Object.keys(data).map(function(k){
    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
  }).join('&');
}

function sendAjaxRequest(method, url, data, handler) {
  let request = new XMLHttpRequest();

  request.open(method, url, true);
  request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
  request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  request.addEventListener('load', handler);
  request.send(encodeForAjax(data));
}

/*********** create an answer ***********/

function sendCreateAnswerRequest(event) {
  let question_id = document.querySelector('#question_id').value;
  let answer = document.querySelector('#answer').value;

  if (answer != '')
    sendAjaxRequest('put', '/api/answer/' + question_id, {answer: answer}, answerAddedHandler);

  event.preventDefault();
}

function answerAddedHandler() {
  //if (this.status != 201) window.location = '/';
  let answer = JSON.parse(this.responseText);

  document.querySelector('#answer').value="";

  // Create the new answer
  let new_answer = createAnswer(answer);

  // Insert the new answer
  let first_answer = document.querySelector('.answer');
  first_answer.parentElement.insertBefore(new_answer, first_answer);
}

function createAnswer(answer) {
  let new_answer = document.createElement('div');
  new_answer.className = 'card'
  new_answer.classList.add('mt-5')
  new_answer.classList.add('answer')
  new_answer.innerHTML = `
    <div class="card-body d-flex justify-content-between">
        <div style="font-size: 2rem">
            <p class="card-text"> ${answer.full_text }</p>
        </div>
        <div class="ml-5">
            <aside class="question-stats">
                <p class="m-0 text-nowrap">0 votes</p>
            </aside>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <p class="m-0">${answer.date}</p>
        <p class="m-0">
            <em>by</em>
            <a href="#"> ${ answer.author }</a>
        </p>
    </div>`;
  return new_answer;
}

/*********** filter questions ***********/

function sendOrderQuestionsRequest(event) {
  let order = this.id;
  console.log(order)
  document.getElementById(this.id).checked = true;

  if (order != '')
    sendAjaxRequest('get', `/api/browse/?order=${order}`, {}, orderedQuestionsHandler);

  event.preventDefault();
}

function orderedQuestionsHandler() {
  let questions = JSON.parse(this.responseText);

  // Create the new answer
  let newQuestions = createQuestions(questions);

  // Insert the new answer
  let parent = document.querySelector('#questions');
  let child = document.querySelector('#questions-list');
  parent.removeChild(child);
  parent.appendChild(newQuestions);
}

function createQuestions(questions) {
  let parent = document.createElement('div');
  parent.id = "questions-list";
  Object.keys(questions).forEach(idx => {
   parent.appendChild(createQuestion(questions[idx]))
  });
  return parent;
}

function createQuestion(question) {
  let new_question = document.createElement('div');
  new_question.className = 'card'
  new_question.classList.add('my-5')

  let tags = "";
  question.tags.forEach(tag => {
    tags += `<span class="badge p-2">${tag.tag_name}</span>\n`
  })
  new_question.innerHTML = 
  `
  <div class="card-body d-flex justify-content-between">
      <div>
          <a
          class="card-title font-weight-bold" 
          href="question/${question.question_id}">
          ${question.title}
          </a>
          <p class="card-text">${question.full_text}</p>
          <div class="flex">
            ${tags}
          </div>
      </div>
      <div class="ml-5">
          <aside class="question-stats">
              <p class="m-0 text-nowrap">${question.num_votes} votes</p>
              <p class="m-0 text-nowrap">${question.num_views} views</p>
              <p class="m-0 text-nowrap">${question.num_answers} answers</p>
          </aside>
      </div>
  </div>
  <div class="card-footer d-flex justify-content-between">
      <p class="m-0">${question.date_distance}</p>
      <p class="m-0">
          <em>by</em>
          <a href="#"> ${question.author_name}</a>
      </p>
  </div>`;

  return new_question;
}

addEventListeners();
