function addEventListeners() {
  let answerCreator = document.querySelector('#add-answer-button');
  if (answerCreator != null)
    answerCreator.addEventListener('click', sendCreateAnswerRequest);

  let userSearch = document.querySelector('#user-search');
  if (userSearch != null) {
    userSearch.addEventListener('input', sendSearchUsersRequest);
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
}

function createAnswer(answer) {
  let new_answer = document.createElement('div');
  new_answer.className = 'card'
  new_answer.classList.add('mt-5')
  new_answer.classList.add('answer')
  new_answer.innerHTML = `
    <div class="card-body d-flex justify-content-between">
        <div style="font-size: 2rem">
            <p class="card-text"> ${answer.full_text}</p>
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

addEventListeners();
