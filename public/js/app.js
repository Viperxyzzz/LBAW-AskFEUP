function addEventListeners() {
  let answerCreator = document.querySelector('#add-answer-button');
  if (answerCreator != null)
    answerCreator.addEventListener('click', sendCreateAnswerRequest);

  let userSearch = document.querySelector('#user-search');
  if (userSearch != null) {
    userSearch.addEventListener('input', sendSearchUsersRequest);
  }

  let answerDelete = document.querySelectorAll('.delete_answer');
  if (answerDelete != null) {
    answerDelete.forEach(
      btn => btn.addEventListener('click', sendDeleteAnswerRequest)
      );
  }

  let orderRadio = document.querySelectorAll('input[name=order]');
  if (orderRadio != null) {
    orderRadio.forEach(orderButton => {
      orderButton.addEventListener('change', sendOrderQuestionsRequest);
    });
  }

  let directionRadio = document.querySelectorAll('input[name=direction]');
  if (directionRadio != null) {
    directionRadio.forEach(directionButton => {
      directionButton.addEventListener('change', sendOrderQuestionsRequest);
    });
  }
}

function encodeForAjax(data) {
  if (data == null) return null;
  return Object.keys(data).map(function (k) {
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
    sendAjaxRequest('put', '/api/answer/' + question_id, { answer: answer }, answerAddedHandler);

  event.preventDefault();
}

function answerAddedHandler() {
  //if (this.status != 201) window.location = '/';
  let answer = JSON.parse(this.responseText);

  document.querySelector('#answer').value = "";

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
            <p class="card-text"> ${answer.full_text}</p>
        </div>
        <div class="ml-5 d-flex">
            <aside class="question-stats">
                <p class="m-0 text-nowrap">0 votes</p>
            </aside>
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
    </div>
    <div class="card-footer d-flex justify-content-between">
        <p class="m-0">${answer.date}</p>
        <p class="m-0">
            <em>by</em>
            <a href="#"> ${answer.author}</a>
        </p>
    </div>`;
  return new_answer;
}

/*********** search for users ***********/

function sendSearchUsersRequest(event) {
  let search = document.querySelector('#user-search').value;

  sendAjaxRequest('get', '/api/users/' + search, {}, userSearchHandler);

  event.preventDefault();
}

function userSearchHandler() {
  //if (this.status != 201) window.location = '/';
  let users = JSON.parse(this.responseText);


  // Create the new users
  let new_element = createUsers(users);

  // Insert the new answer
  let old_users = document.getElementById('users-list');
  let parent = old_users.parentElement;

  old_users.remove()
  parent.appendChild(new_element);
}

function createUsers(users) {
  let new_users = document.createElement('div');
  new_users.className = 'd-flex'
  new_users.classList.add('flex-wrap')
  new_users.id = "users-list"
  console.log(users)
  if (users.length == 0) {
    new_users.innerHTML = '<p>No results match the criteria.</p>'
  }
  Object.values(users).forEach(user => {
    new_users.appendChild(createUser(user))
  });
  return new_users;
}

function createUser(user) {
  let new_user = document.createElement('div');
  //new_users.className = 'col-lg-10'
  //new_users.id = "users-list"
  new_user.innerHTML = `
  <div class="card d-flex flex-row m-3 p-2 bg-light" style="width: 250px;">
      <div class="align-self-center">
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="img-fluid rounded-start" alt="user image" width="60px">
      </div>
      <div class="card-body mx-2 p-2">
          <h4 class="card-title m-0 p-0">
              <a href="#">${user.username}</a>
          </h4>
          <p class="card-body m-0 p-0">${user.name}</p>
          <p class="card-body m-0 p-0">${user.score} points</p>
      </div>
  </div>`;
  return new_user;
}

/*********** delete an answer ***********/

function sendDeleteAnswerRequest(event) {
  let answer_id = event.target.parentElement.children[0].innerHTML;
  console.log(answer_id)

  if (answer != '')
    sendAjaxRequest('delete', '/api/answer/delete/' + answer_id, {}, answerDeletedHandler);
  event.preventDefault();
}

/*********** filter questions ***********/

function sendOrderQuestionsRequest(event) {
  let order = document.querySelector('input[name="order"]:checked').id;
  let direction = document.querySelector('input[name="direction"]:checked').id;

  if (order != '')
    sendAjaxRequest('get', `/api/browse/?order=${order}&direction=${direction}`, {}, orderedQuestionsHandler);
    
  event.preventDefault();
}

function answerDeletedHandler() {
  //if (this.status != 202) window.location = '/';
  let deletedAnswer = JSON.parse(this.responseText);

  document.querySelector('#answer').value="";

  let deletedAnswerElement = document.getElementById("answer_" + deletedAnswer.answer_id)
  deletedAnswerElement.remove();
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

function editPass(){
  let editButton = document.getElementById('edit-pass')
  editButton.setAttribute("class", "hide")

  let divPass = document.getElementById("edit-pass-div")
  divPass.setAttribute("class", "form-gorup")
  divPass.innerHTML =
  `
    <label>New Password</label>
    <input type="password" name="new_password">
  `

  let divPassConfirm = document.getElementById("edit-pass-div-conf")
  divPassConfirm.setAttribute("class", "form-gorup")
  divPassConfirm.innerHTML =
  `
    <label>Confirm Password</label>
    <input type="password" name="confirm_pass">
  `
}

function submitSettings(){
  document.getElementById("edit-user-form").submit();
}


addEventListeners();
