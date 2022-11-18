function addEventListeners() {
  let answerCreator = document.querySelector('#add-answer-button');
  if (answerCreator != null)
    answerCreator.addEventListener('click', sendCreateAnswerRequest);

  let answerDelete = document.querySelectorAll('.delete_answer');
  if (answerDelete != null) {
    answerDelete.forEach(
      btn => btn.addEventListener('click', sendDeleteAnswerRequest)
      );
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
  addEventListeners();
}

function createAnswer(answer) {
  let new_answer = document.createElement('div');
  new_answer.className = 'card'
  new_answer.classList.add('mt-5')
  new_answer.classList.add('answer')
  new_answer.id = 'answer_' + answer.answer_id;
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
      <div class="dropdown">
          <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
              <i class="material-symbols-outlined">more_vert</i>
          </button>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
              <data class="answer_id" hidden> ${answer.answer_id }</data>
              <button class="dropdown-item edit_answer">
                  <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                  Edit
              </button>
              <button class="dropdown-item delete_answer">
                  <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                  Delete
              </button>
          </div>
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

/*********** delete an answer ***********/

function sendDeleteAnswerRequest(event) {
  let answer_id = event.target.parentElement.children[0].innerHTML;
  console.log(answer_id)

  if (answer != '')
    sendAjaxRequest('delete', '/api/answer/delete/' + answer_id, {}, answerDeletedHandler);

  event.preventDefault();
}

function answerDeletedHandler() {
  //if (this.status != 202) window.location = '/';
  let deletedAnswer = JSON.parse(this.responseText);

  document.querySelector('#answer').value="";

  let deletedAnswerElement = document.getElementById("answer_" + deletedAnswer.answer_id)
  deletedAnswerElement.remove();
}

addEventListeners();
