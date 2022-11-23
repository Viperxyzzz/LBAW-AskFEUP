function addEventListeners() {
  let answerCreator = document.querySelector('#add-answer-button');
  if (answerCreator != null)
    answerCreator.addEventListener('click', sendCreateAnswerRequest);

  let enterInputAnswerCreator = document.getElementById('answer');
  enterInputAnswerCreator.addEventListener('keypress', function(event) {
      if (event.key === 'Enter')
      {
        event.preventDefault();
        sendCreateAnswerRequest(event);
      }
    });

  let userSearch = document.querySelector('#user-search');
  if (userSearch != null) {
    userSearch.addEventListener('input', sendSearchUsersRequest);
  }

  let orderUserRadio = document.querySelectorAll('input[name=order-users]');
  if (orderUserRadio != null) {
    orderUserRadio.forEach(orderUserButton => {
      orderUserButton.addEventListener('change', sendSearchUsersRequest);
    });
  }

  let directionUsersRadio = document.querySelectorAll('input[name=direction-users]');
  if (directionUsersRadio != null) {
    directionUsersRadio.forEach(directionUsersButton => {
      directionUsersButton.addEventListener('change', sendSearchUsersRequest);
    });
  }

  let answerEdit = document.querySelectorAll('.edit_answer');
  if (answerEdit != null) {
    answerEdit.forEach(
      btn => btn.addEventListener('click', editAnswer)
      );
  }

  let answerDelete = document.querySelectorAll('.delete-answer');
  if (answerDelete != null) {
    answerDelete.forEach(
      btn => btn.addEventListener('click', sendDeleteAnswerRequest)
      );
  }

  let orderQuestionsRadio = document.querySelectorAll('input[name=order-questions]');
  if (orderQuestionsRadio != null) {
    orderQuestionsRadio.forEach(orderQuestionsButton => {
      orderQuestionsButton.addEventListener('change', sendOrderQuestionsRequest);
    });
  }

  let directionQuestionsRadio = document.querySelectorAll('input[name=direction-questions]');
  if (directionQuestionsRadio != null) {
    directionQuestionsRadio.forEach(directionQuestionsButton => {
      directionQuestionsButton.addEventListener('change', sendOrderQuestionsRequest);
    });
  }

  let profileTabs = document.querySelectorAll('.profile-nav')
  profileTabs.forEach(
    button => {
      button.addEventListener('click', function(){
        toggleProfileTab(button.id + '-tab')
        button.classList.add('active');
      })
    }
  )
}

function closeProfileTabs() {
  let buttons = document.querySelectorAll('.profile-nav');
  buttons.forEach(button => button.classList.remove('active'))
  let tabs = document.querySelectorAll('.profile-tab');
  tabs.forEach(tab => {tab.classList.remove('profile-tab-open')});
}

function toggleProfileTab(tab) {
  closeProfileTabs();
  let open = document.getElementById(tab);
  if (open != null)
    open.classList.add('profile-tab-open');
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
  console.log("create request function")
  let question_id = document.querySelector('#question_id').value;
  console.log(question_id)
  let answer = document.querySelector('#answer').value;
  console.log(answer)

  if (answer != '')
    sendAjaxRequest('put', '/api/answer/' + question_id, { answer: answer }, answerAddedHandler);

  event.preventDefault();
}

function answerAddedHandler() {
  if (this.status != 200) window.location = '/login';
  let answer = JSON.parse(this.responseText);

  document.querySelector('#answer').value = "";

  // Create the new answer
  let new_answer = createAnswer(answer);

  // Insert the new answer
  let answers = document.querySelector('#answers');
  answers.prepend(new_answer);
}

function createAnswer(answer) {
  let new_answer = document.createElement('div');
  new_answer.className = 'card'
  new_answer.classList.add('mt-5')
  new_answer.classList.add('answer')
  new_answer.id = 'answer_' + answer.answer_id;
  new_answer.innerHTML = ` 
    <div class="card-body d-flex justify-content-between">
        <div style="font-size: 2rem" class="answer-full-text">
            <p class="card-text">${answer.full_text}</p>
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
                    <data class="answer_id" hidden>${answer.answer_id }</data>
                    <button class="dropdown-item edit_answer" onclick="editAnswer(event)">
                        <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                        Edit
                    </button>
                    <button class="dropdown-item delete_answer" onclick="sendDeleteAnswerRequest(event)">
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
            <a href="/users/${answer.user_id}"> ${answer.author}</a>
        </p>
    </div>`;
  return new_answer;
}

/*********** search for users ***********/

function sendSearchUsersRequest(event) {
  let order = document.querySelector('input[name="order-users"]:checked').id;
  let direction = document.querySelector('input[name="direction-users"]:checked').id;
  let search = document.querySelector('#user-search').value;

  sendAjaxRequest('get', `/api/users/?search=${search}&order=${order}&direction=${direction}`, {}, userSearchHandler);

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
        <img src="/storage/${user.picture_path}.jpeg" class="img-fluid rounded-circle" alt="user image" width="60px">
      </div>
      <div class="card-body mx-2 p-2">
          <h4 class="card-title m-0 p-0">
              <a href="/users/${user.user_id}">${user.username}</a>
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

/*********** filter questions ***********/

function sendOrderQuestionsRequest(event) {
  let order = document.querySelector('input[name="order-questions"]:checked').id;
  let direction = document.querySelector('input[name="direction-questions"]:checked').id;
  const urlParams = new URLSearchParams(window.location.search);
  const search = urlParams.get('searchText');

  if (order != '')
    sendAjaxRequest('get', `/api/browse/?order=${order}&direction=${direction}&searchText=${search}`, {}, orderedQuestionsHandler);
    
  event.preventDefault();
}

function orderedQuestionsHandler() {
  let questions = JSON.parse(this.responseText);

  if (questions.length > 0) {
    let newQuestions = createQuestions(questions);

    let parent = document.querySelector('#questions');
    let child = document.querySelector('#questions-list');
    parent.removeChild(child);
    parent.appendChild(newQuestions);
  }
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
          <a href="/users/${question.author_id}"> ${question.author_name}</a>
      </p>
  </div>`;

  return new_question;
}

/***********  ***********/
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

/*********** create an edit answer card ***********/

function editAnswer(event) {
  let answer_id = event.target.parentElement.children[0].innerText;

  let answer = document.querySelector('#answer_' + answer_id);
  let text = answer.querySelector('.card-text').innerText;
  let full_text = answer.querySelector('.answer-full-text');
  full_text.insertAdjacentElement("afterend", createAnswerForm(answer_id, text));
  full_text.remove();
}

function createAnswerForm(answer_id, text) {
  let answer_form = document.createElement('div');
  let answer = document.getElementById(answer_id);
  //answer_form.classList.add('mt-5')
  answer_form.classList.add('answer-form')
  answer_form.style.width = "100%"
  answer_form.id = `answer_form_${answer_id}`

  setInnerHTML( answer_form,
    `
    <input type="hidden" name="answer_id" id="answer_id" value="${answer_id}"></input>
    <input type="hidden" name="answer" id="answer" value="${answer}" style="height:200px"></input>
      <div class="form-group" style="margin: 0">
      <input type="submit" style="display: none" />
          <input id="full_text" type="text" name="full_text" class="form-control-lg edit-text answer-update" value="${text}" required/>
      </div>
      <div class="text-right">
          <button id="update-answer-button" onclick="answerUpdater()" type="submit" class="m-0">
              Save Changes
          </button>
      </div>
      <script>
      var input = document.getElementById("full_text");
      input.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
          event.preventDefault();
          document.getElementById("update-answer-button").click();
        }
      });
      </script>
`)
  return answer_form;
}

function answerUpdater() {
  let new_text = document.querySelector('#full_text').value;
  let answer_id = document.querySelector('#answer_id').value;
  sendAjaxRequest('post', '/api/answer/update/' + answer_id, {full_text: new_text, was_edited: true }, sendCreateAnswerUpdateRequest);
}


function sendCreateAnswerUpdateRequest() {
  let answer = JSON.parse(this.responseText);

  let p = document.createElement('p');
  p.innerText = answer.full_text;

  let answer_element = document.querySelector('#answer_' + answer.answer_id);
  let answer_form = answer_element.querySelector('.answer-form');
  answer_form.insertAdjacentElement("afterend", p);
  answer_form.remove();

}

function setInnerHTML(elm, html) {
  elm.innerHTML = html;

  Array.from(elm.querySelectorAll("script"))
    .forEach( oldScriptEl => {
      const newScriptEl = document.createElement("script");

      Array.from(oldScriptEl.attributes).forEach( attr => {
        newScriptEl.setAttribute(attr.name, attr.value)
      });
      const scriptText = document.createTextNode(oldScriptEl.innerHTML);
      newScriptEl.appendChild(scriptText);
      oldScriptEl.parentNode.replaceChild(newScriptEl, oldScriptEl);
  });
}

/***********  ***********/

addEventListeners();