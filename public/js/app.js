function addEventListeners() {
  let answerCreator = document.querySelector('#add-answer-button');
  if (answerCreator != null)
    answerCreator.addEventListener('click', sendCreateAnswerRequest);


  let userDelete = document.querySelectorAll('.delete-user');
  if (userDelete != null) {
    userDelete.forEach(
      btn => btn.addEventListener('click', sendDeleteUserRequest)
      );
  }

  let commentEdit = document.querySelectorAll('.edit_comment');
  if (commentEdit != null) {
    commentEdit.forEach(
      btn => btn.addEventListener('click', editComment)
      );
  }
  let commentDelete = document.querySelectorAll('.delete-comment');
  if (commentDelete != null) {
    commentDelete.forEach(
      btn => btn.addEventListener('click', sendDeleteCommentRequest)
    );
  }

  let enterInputAnswerCreator = document.getElementById('answer');
  if(enterInputAnswerCreator != null)
    enterInputAnswerCreator.addEventListener('keypress', function(event) {
      if (event.key === 'Enter')
      {
        event.preventDefault();
        sendCreateAnswerRequest(event);
      }
    });

  let filterOptions = document.querySelectorAll('.filter-option');
  if (filterOptions != null) {
    filterOptions.forEach((btn) => {
      if (btn.querySelector('input').checked) btn.classList.toggle('active');
      btn.addEventListener(
      'click', (event) => event.target.classList.toggle('active'));
    }
    )
  }

  let commentFormCreator = document.querySelectorAll('.add-comment-answer-form-button');
  if (commentFormCreator != null) {
    commentFormCreator.forEach(button =>{
      button.addEventListener('click', answerCommentForm);
  });
  }

  let commentQuestionFormCreator = document.querySelectorAll('.add-comment-question-form-button');
  if (commentQuestionFormCreator.length > 0)
      commentQuestionFormCreator[0].addEventListener('click', questionCommentForm);

  let commentCreator = document.querySelectorAll('#add-comment-button');
  if (commentCreator != null) {
    commentCreator.forEach(button =>{
      button.addEventListener('click', function(){alert("test")});
  });
  }

  let enterInputEditUserFullName = document.querySelector('#edit-full-name');
  if(enterInputEditUserFullName != null)
    enterInputEditUserFullName.addEventListener('keypress', function(event) {
      if (event.key === 'Enter'){
        document.getElementById("edit-username").focus();
      }
  });

  let enterInputEditUserUsername = document.querySelector('#edit-username');
  if(enterInputEditUserUsername != null)
    enterInputEditUserUsername.addEventListener('keypress', function(event) {
      if (event.key === 'Enter'){
        document.getElementById("edit-email").focus();
      }
  });

  let enterInputEditUserEmail = document.querySelector('#edit-email');
  if(enterInputEditUserEmail != null)
    enterInputEditUserEmail.addEventListener('keypress', function(event) {
      if (event.key === 'Enter'){
        document.getElementById("save-settings").focus();
      }
  });

  let userSearch = document.querySelector('#user-search');
  if (userSearch != null) {
    userSearch.addEventListener('input', sendSearchUsersRequest);
  }

  let tagsSearch = document.querySelector('#tags-search');
  if (tagsSearch != null) {
    tagsSearch.addEventListener('input', sendSearchTagsRequest);
  }

  let tagsFilter = document.querySelectorAll('.topic-check');
  if (tagsFilter != null) {
    tagsFilter.forEach(
      check => check.addEventListener('input', sendSearchTagsRequest)
    );
  }

  let createTag = document.querySelector('.add-tag');
  if (createTag != null) {
    createTag.addEventListener('click', sendCreateTagRequest)
  }

  let editTag = document.querySelectorAll('.edit-tag');
  if (editTag != null) {
    editTag.forEach(
      tag => tag.addEventListener('click', sendEditTagRequest)
    );
  }

  let removeTag = document.querySelectorAll('.delete-tag');
  if (removeTag != null) {
    removeTag.forEach(
      rTag => rTag.addEventListener('click', sendRemoveTagsRequest)
    );
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

  let reportDelete = document.querySelectorAll('.delete-report');
  if (reportDelete != null) {
    reportDelete.forEach(
      btn => btn.addEventListener('click', sendDeleteReportRequest)
      );
  }

  let addBlock = document.querySelectorAll('.add-block');
  if (addBlock != null) {
    addBlock.forEach(
      btn => btn.addEventListener('click', sendCreateBlockRequest)
      );
  }

  let removeBlock = document.querySelectorAll('.unblock-user');
  if (removeBlock != null) {
    removeBlock.forEach(
      btn => btn.addEventListener('click', sendRemoveBlockRequest)
      );
  }

  let addReport = document.querySelectorAll('.add-report');
  if (addReport != null) {
    addReport.forEach(
      btn => btn.addEventListener('click', sendCreateReportRequest)
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

  let tagFilter = document.querySelectorAll('.tag-filter');
  if (tagFilter != null) {
    tagFilter.forEach(tag => {
      tag.addEventListener('click', sendOrderQuestionsRequest);
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

  let dashboardTabs = document.querySelectorAll('.dashboard-tab-button')
  dashboardTabs.forEach(
    button => {
      button.addEventListener('click', function(){
        toggleDashboardTab(button.id + '-tab')
        button.classList.add('active');
      })
    }
  )

  let followTag = document.querySelectorAll('.follow-tag')
  followTag.forEach(
    button => {
      button.addEventListener('click', sendFollowTagRequest)
    }
  )

  let unFollowTag = document.querySelectorAll('.unFollow-tag')
  unFollowTag.forEach(
    button => {
      button.addEventListener('click', sendUnFollowTagRequest)
    }
  )

  let followQuestion = document.querySelectorAll('.follow-question')
  followQuestion.forEach(
    button => {
      button.addEventListener('click', sendFollowQuestionRequest)
    }
  )

  let unFollowQuestion = document.querySelectorAll('.un-follow-question')
  unFollowQuestion.forEach(
    button => {
      button.addEventListener('click', sendUnFollowQuestionRequest)
    }
  )
  
  let notificationUpdate = document.querySelectorAll('.button-notification');
  if (notificationUpdate != null) {
    notificationUpdate.forEach(
      btn => btn.addEventListener('click', sendUpdateNotificationRequest)
      );
  }

  let updateVotes = document.querySelectorAll('.update-votes');
  if (updateVotes != null) {
    updateVotes.forEach(
      btn => btn.addEventListener('click', sendUpdateVotesRequest)
      );
  }

  let updateVotesAnswer = document.querySelectorAll('.update-votes-answer');
  if (updateVotesAnswer != null) {
    updateVotesAnswer.forEach(
      btn => btn.addEventListener('click', sendUpdateVotesAnswerRequest)
      );
  }

  let updateVotesComment = document.querySelectorAll('.update-votes-comment');
  if (updateVotesComment != null) {
    updateVotesComment.forEach(
      btn => btn.addEventListener('click', sendUpdateVotesCommentRequest)
      );
  }
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

function closeDashboardTabs() {
  let buttons = document.querySelectorAll('.dashboard-tab-button');
  buttons.forEach(button => button.classList.remove('active'))
  let tabs = document.querySelectorAll('.dashboard-tab');
  tabs.forEach(tab => {tab.classList.remove('tab-open')});
}

function toggleDashboardTab(tab) {
  closeDashboardTabs();
  let open = document.getElementById(tab);
  if (open != null)
    open.classList.add('tab-open');
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
    sendAjaxRequest('post', '/api/answer/' + question_id, { answer: answer }, answerAddedHandler);

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
  <div class="modal fade" id="answerModal_${answer.answer_id}" tabindex="-1" aria-labelledby="answerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="answerModalLabel">Delete answer</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="material-symbols-outlined">close</i>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this answer?
        </div>
        <div class="modal-footer border-0">
          <input type="hidden" name="answer_id" value="${answer.answer_id}">
          <button type="button" class="button-outline" data-dismiss="modal">Close</button>
          <button type="button" onclick=sendDeleteAnswerRequest(event) class="button delete-answer" data-dismiss="modal">Confirm</button>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body d-flex justify-content-between">
      <div class="flex-fill">
          <p class="m-0">
              <img src="/storage/${answer.author.picture_path}.jpeg" class="img-fluid rounded-circle" alt="user image" width="25px">
              <a class="font-weight-bold" href="/users/${answer.user_id}"> ${answer.author.name}</a>
          </p>
          <div class="answer-full-text">
              <p class="card-text pb-5 pt-2">${answer.full_text}</p>
          </div>
      </div>
      <div class="ml-5 d-flex">
          <aside class="question-stats">
          </aside>
              <div class="dropdown">
                  <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
                      <i class="material-symbols-outlined">more_vert</i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                          <data class="answer_id" hidden>${answer.answer_id}</data>
                          <button class="dropdown-item edit_answer" type="button" onclick=editAnswer(event)>
                              <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                              Edit
                          </button>
                      <input type="hidden" name="answer_id" value="${answer.answer_id}">
                      <button class="dropdown-item" type="button"  data-toggle="modal" data-target="#answerModal_${answer.answer_id}">
                          <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                          Delete
                      </button>
                  </div>
              </div>
      </div>
  </div>
  <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-start mt-2">
          <button class="button-clear m-0 px-1 update-votes-answer" type="button" onclick=sendUpdateVotesAnswerRequest(event)>
              <input type="hidden" name="vote" value="1"></input>
              <input type="hidden" name="answer_id" value="${answer.answer_id}"></input>
              <i width="16" height="16" class="material-symbols-outlined ">arrow_upward</i>
          </button>
          <p class="m-0 px-1 pt-1" id="num-votes-answer-${answer.answer_id}">${answer.num_votes}</p>
          <button class="button-clear m-0 px-1 update-votes-answer" type="button" onclick=sendUpdateVotesAnswerRequest(event)>
              <input type="hidden" name="vote" value="-1"></input>
              <input type="hidden" name="answer_id" value="${answer.answer_id}"></input>
              <i width="16" height="16" class="material-symbols-outlined ">arrow_downward</i>
          </button>
          <button class="button-clear m-0 px-1" type="button">
              <i width="12" height="12" class="material-symbols-outlined" onclick=answerCommentForm(event)>chat_bubble</i>
          </button>
      </div>
      <p class="m-0">${answer.date}</p>
  </div>
  <div class="answer-comments">
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

/*********** search for tags ***********/

function sendSearchTagsRequest(event) {
  let search = document.querySelector('#tags-search').value;
  let topics = document.querySelectorAll('.topic-check');
  let topicsStr = '';
  topics.forEach(
    topic => {
      topicsStr += (topic.checked) ? `&topics[]=${topic.value}` : '';
    }
  )

  sendAjaxRequest('get', `/api/tags/?search=${search}${topicsStr}`, {}, tagsSearchHandler);

  event.preventDefault();
}

function tagsSearchHandler() {
  //if (this.status != 201) window.location = '/';
  let response = JSON.parse(this.responseText);


  // Create the new tags
  let new_element = createTags(response);

  // Insert the new tags
  let old_element = document.getElementById('tags-list');
  let parent = old_element.parentElement;

  old_element.remove()
  parent.appendChild(new_element);
}

/*********** create tags ***********/

function sendCreateTagRequest(event) {
  let body = event.target.parentElement.parentElement.querySelector('.modal-body')
  let name = body.querySelector('input[name=name]').value
  let description = body.querySelector('input[name=description]').value
  let topic = body.querySelector('#topics > option:checked').value
  let data = {name : name, description : description, topic : topic}

  if (name != null)
    sendAjaxRequest('post', `/api/tag/create`, data, tagCreatedHandler);

  event.preventDefault()
}

function tagCreatedHandler() {
  //if (this.status != 201) window.location = '/';
  let response = JSON.parse(this.responseText);

  let tag_element = createTag(response.tag, response.topics)

  document.querySelector('#tags-list').prepend(tag_element)
}

/*********** edit tags ***********/

function sendEditTagRequest(event) {
  let body = event.target.parentElement.parentElement.querySelector('.modal-body')
  let id = body.querySelector('input[name=id]').value
  let name = body.querySelector('input[name=name]').value
  let description = body.querySelector('input[name=description]').value
  let topic = body.querySelector('#topics > option:checked').value
  let data = {name : name, description : description, topic : topic}

  if (id != null)
    sendAjaxRequest('put', `/api/tag/edit/${id}`, data, tagEditedHandler);

  event.preventDefault()
}

function tagEditedHandler() {
  let tag = JSON.parse(this.responseText);

  let tag_element = document.getElementById(`tag-${tag.tag_id}`)
  tag_element.querySelector('.card-body > p').innerHTML = tag.tag_description
  tag_element.querySelector('.card-header > a').innerHTML = tag.tag_name
}

/*********** remove tags ***********/

function sendRemoveTagsRequest(event) {
  let id = event.target.parentElement.children[1].value

  if (id != null)
    sendAjaxRequest('delete', `/api/tag/delete/${id}`, {}, tagDeletedHandler);

  event.preventDefault();
}

function tagDeletedHandler() {
  //if (this.status != 201) window.location = '/';
  let tag = JSON.parse(this.responseText);

  let tag_element = document.getElementById(`tag-${tag.tag_id}`)
  tag_element.remove()
  document.querySelector('.modal-backdrop').remove()
}

function createTags(response) {
  let new_tags = document.createElement('ul');
  new_tags.className = 'd-flex'
  new_tags.classList.add('flex-wrap')
  new_tags.id = "tags-list"
  if (response.tags.length == 0) {
    new_tags.innerHTML = '<p>No results match the criteria.</p>'
  }
  Object.values(response.tags).forEach(tag => {
    new_tags.appendChild(createTag(tag, response.topics))
  });
  return new_tags;
}

function createTag(tag, topics) {
  let new_tag = document.createElement('li');
  new_tag.className = 'card'
  new_tag.classList.add('m-3')
  new_tag.style = "width: 250px"
  new_tag.id = `tag-${tag.tag_id}`
  let html = `
  <div class="card-header d-flex align-items-start justify-content-between">
      <a href="/browse/?tags[]=${ tag.tag_id }" class="badge p-3 m-1 mt-2">${tag.tag_name}</a>
      <div class="d-flex justify-content-end">`

  if (tag['following']) {
    html +=
      `<button class="unFollow-tag button-clear px-2 pr-3 pb-2 d-flex" id="unFollow-tag-${tag.tag_id}" onClick="sendUnFollowTagRequest(event)">
          <input type="hidden" value="${tag.tag_id}">
          <i class="p-0 pt-2 material-symbols-outlined">done</i>
          <p class="pb-2">Following</p>
      </button>`
  }
  else {
    html +=
      `<button class="follow-tag button-clear px-2 pr-3 pb-2 d-flex" id="follow-tag-${tag.tag_id}" onClick="sendFollowTagRequest(event)">
          <input type="hidden" value="${tag.tag_id}">
          <i class="p-0 pt-2 material-symbols-outlined">add</i>
          <p class="pb-2">Follow</p>
      </button>`
  }

  if (tag['manage']) {
    html +=
      `<div class="dropdown">
          <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
              <i class="material-symbols-outlined">more_vert</i>
          </button>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
              <input type="hidden" name="question_id" value="${tag.tag_id}">
              <button class="dropdown-item edit-tag" type="button" data-toggle="modal" data-target="#edit-tag-modal-${tag.tag_id}">
                  <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                  Edit
              </button>
              <button class="dropdown-item" type="button" data-toggle="modal" data-target="#remove-tag-modal-${tag.tag_id}">
                  <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                  Delete
              </button>
          </div>
      </div>`
  }

  html +=`</div>
    </div>
    <div class="card-body">
        <p>${tag.tag_description}</p>
    </div>`;

  html += createTagModals(tag, topics);
  html += '</li>'

  new_tag.innerHTML = html;
  return new_tag;
}

function createTagModals(tag, topics) {

  let html = '';
  if (tag['manage']) {
    html +=
    `<!-- Delete tag modal box -->
    <div class="modal fade" id="remove-tag-modal-${tag.tag_id}" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="questionModalLabel">Delete tag</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-symbols-outlined">close</i>
            </button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this tag?</p>
        </div>
        <div class="modal-footer border-0">
            <button type="button" class="button-outline" data-dismiss="modal">Close</button>
            <input type="hidden" value="${tag.tag_id}">
            <button class="button delete-tag" data-dismiss="modal" type="button" onclick="sendRemoveTagsRequest(event)">
                Confirm
            </button>
          </div>
        </div>
    </div>
    </div>

    <!-- Edit tag modal box -->
    <div class="modal fade" id="edit-tag-modal-${tag.tag_id}" tabindex="-1" aria-labelledby="editTagModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title c-primary" id="editTagModalLabel">Edit tag</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-symbols-outlined">close</i>
            </button>
          </div>
            <div class="modal-body">
                <input type="hidden" name="id" value=${tag.tag_id} required>
                <h5>Name</h5>
                <input type="text" name="name" value=${tag.tag_name} required>
                <h5>Description</h5>
                <input type="text" name="description" value="${tag.tag_description}" required>
                <label class="title-blue" for="topics">Topics</label>
                <select class="form-control" id="topics" name="topic" size="6">`

    Object.values(topics).forEach(topic => {
      html +=
          `<option value="${topic.topic_id}"`
      if (tag.topic_id == topic.topic_id) {
        html += ' selected '
      }
      html += `>${topic.topic_name}</option>`
    });

    html +=
                `</select>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="button-outline" data-dismiss="modal">Close</button>
                <button class="button" data-dismiss="modal" type="submit" onclick="sendEditTagRequest(event)">
                    Confirm
                </button>
            </form> 
          </div>
      </div>
    </div>`
  }
  return html;
}

/*********** delete an answer ***********/

function sendDeleteAnswerRequest(event) {
  let answer_id = event.target.parentElement.children[0].value;

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

/*********** delete a report ***********/

function sendDeleteReportRequest(event) {
  let report_id = event.target.parentElement.children[0].innerText;

  if (report_id != '')
    sendAjaxRequest('delete', '/api/report/delete/' + report_id, {}, reportDeletedHandler);
  event.preventDefault();
}

function reportDeletedHandler() {
  //if (this.status != 202) window.location = '/';
  let deletedReport = JSON.parse(this.responseText);

  let deletedReportElement = document.getElementById("report_" + deletedReport.report_id)
  deletedReportElement.remove();
}

/*********** create block ***********/

function sendCreateBlockRequest(event) {
  let body = event.target.parentElement.parentElement.querySelector('.modal-body')
  let user_id = body.querySelector('input[name=user_id]').value
  let reason = body.querySelector('input[name=reason]').value

  if (user_id != null)
    sendAjaxRequest('post', `/api/blocks/add/${user_id}`, {reason : reason}, blockCreatedHandler);

  event.preventDefault()
}

function blockCreatedHandler() {
  if (this.status != 201) return;

  info = document.querySelector('.profile-info')

  warning = document.createElement('a');
  warning.href = '/dashboard'
  warning.classList.add('warning-blocked', 'd-flex', 'ml-5', 'p-2', 'border', 'border-danger', 'rounded', 'align-items-baseline')
  warning.innerHTML = 
  `<h4 class="m-0 text-danger">
        <i class="p-0 material-symbols-outlined">warning</i>
        This user is blocked!
    </h4>
    `
  info.prepend(warning)

  document.querySelector('.block-user').classList.toggle('d-flex')
  document.querySelector('.block-user').classList.toggle('tab-closed')
  document.querySelector('.unblock-user').classList.toggle('d-flex')
  document.querySelector('.unblock-user').classList.toggle('tab-closed')
}

/*********** remove block ***********/

function sendRemoveBlockRequest(event) {
  let user_id = event.target.querySelector('input[name=user_id]').value

  if (user_id != null)
    sendAjaxRequest('delete', `/api/blocks/delete/${user_id}`, {}, blockRemovedHandler);

  event.preventDefault()
}

function blockRemovedHandler() {
  if (this.status != 200) return;

  document.querySelector('.warning-blocked').remove()

  document.querySelector('.block-user').classList.toggle('d-flex')
  document.querySelector('.block-user').classList.toggle('tab-closed')
  document.querySelector('.unblock-user').classList.toggle('d-flex')
  document.querySelector('.unblock-user').classList.toggle('tab-closed')
}

/*********** create a report ***********/

function sendCreateReportRequest(event) {
  let body = event.target.parentElement.parentElement.querySelector('.modal-body')
  let reason = body.querySelector('input[name=reason]').value
  let question_id = body.querySelector('input[name=question_id]').value
  let answer_id = body.querySelector('input[name=answer_id]').value
  let comment_id = body.querySelector('input[name=comment_id]').value
  data = {reason: reason, question_id : question_id, answer_id : answer_id, comment_id : comment_id}

  if (body != null)
    sendAjaxRequest('post', '/api/report/create', data, () => {});
  event.preventDefault();
}

/*********** filter questions ***********/

function sendOrderQuestionsRequest(event) {
  let order = document.querySelector('input[name="order-questions"]:checked').id;
  let direction = document.querySelector('input[name="direction-questions"]:checked').id;
  let tags = document.querySelectorAll('.tag-filter');
  let tagsStr = '';
  tags.forEach(
    tag => {
      tagsStr += (tag.hasAttribute('selected')) ? `&tags[]=${tag.value}` : '';
    }
  )

  const urlParams = new URLSearchParams(window.location.search);
  const search = urlParams.get('searchText');

  if (order != '')
    sendAjaxRequest('get', `/api/browse/?order=${order}&direction=${direction}${(search !== null) ? '&searchText=' + search : ''}${tagsStr}`, {}, orderedQuestionsHandler);
    
  event.preventDefault();
}

function orderedQuestionsHandler() {
  let questions = JSON.parse(this.responseText);

  if (Object.keys(questions).length > 0) {
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
    tags += `<a href="/browse/?tags[]=${ tag.tag_id }" class="badge p-3 m-1 mt-1">${tag.tag_name}</a>\n`
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

/*********** edit answer ***********/

function editAnswer(event) {
  removeOpenedForms()

  let answer_id = event.target.parentElement.children[0].innerText;

  let answer = document.querySelector('#answer_' + answer_id);
  let text = answer.querySelector('.card-text').innerText;
  let full_text = answer.querySelector('.answer-full-text');
  full_text.insertAdjacentElement("afterend", createAnswerForm(answer_id, text));
  full_text.innerHTML = '';
}

function createAnswerForm(answer_id, text) {
  let answer_form = document.createElement('div');
  let answer = document.getElementById(answer_id);
  answer_form.classList.add('answer-form')
  answer_form.classList.add('w-100')
  answer_form.id = `answer_form_${answer_id}`

  // prevent duplicated edit form
  let previous_comment_form = document.querySelector(`#answer_form_${answer_id}`)
  if(previous_comment_form!=null&&previous_comment_form.innerHTML!='') return previous_comment_form;

  setInnerHTML(   answer_form, 
    `
    <input type="hidden" name="answer_id" id="answer_id" value="${answer_id}"></input>
    <input type="hidden" name="answer" id="answer" value="${answer}"></input>
    <textarea id="full_text" rows="4" type="text" name="full_text" class="edit-text mt-2" required/>${text}</textarea>
    <div class = "row justify-content-between" style="padding: 0.75rem 0.75rem;">
    <button class="cancel-add-comment button-clear px-2 pr-3 pb-2 d-flex" style="margin: 0;" onclick="cancelEditAnswer(${answer_id},'${text}')">
        <p class="pb-2">Cancel</p>
    </button>
    <button id="update-answer-button" onclick="answerUpdater()" type="submit" class="m-0">
    Save Changes
    </button>
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

function cancelEditAnswer(answer_id,text){
  let p = document.createElement('p');
  p.classList.add('card-text', 'pb-5', 'pt-2');
  p.innerText = text;

  let answer_element = document.querySelector('#answer_' + answer_id);
  let answer_form = answer_element.querySelector('.answer-form');
  answer_form.parentElement.querySelector('.answer-full-text').appendChild(p);

  answer_form.remove();

  // Insert answer form back
  addAnswerCard();

}

function answerUpdater() {
  let new_text = document.querySelector('#full_text').value;
  let answer_id = document.querySelector('#answer_id').value;
  addAnswerCard();
  sendAjaxRequest('put', '/api/answer/update/' + answer_id, {full_text: new_text, was_edited: true }, sendCreateAnswerUpdateRequest);
}


function sendCreateAnswerUpdateRequest() {
  let answer = JSON.parse(this.responseText);

  let p = document.createElement('p');
  p.classList.add('card-text', 'pb-5', 'pt-2');
  p.innerText = answer.full_text;

  let answer_element = document.querySelector('#answer_' + answer.answer_id);
  let answer_form = answer_element.querySelector('.answer-form');
  answer_form.parentElement.querySelector('.answer-full-text').appendChild(p);

  answer_form.remove();
  addAnswerCard();

}
function cancelEditAnswer(answer_id,text){
  let p = document.createElement('p');
  p.classList.add('card-text', 'pb-5', 'pt-2');
  p.innerText = text;

  let answer_element = document.querySelector('#answer_' + answer_id);
  let answer_form = answer_element.querySelector('.answer-form');
  answer_form.parentElement.querySelector('.answer-full-text').appendChild(p);

  answer_form.remove();

  // Insert answer form back
  addAnswerCard();
}
/***********  ***********/
function sendFollowTagRequest(event) {
  let tag_id = event.currentTarget.querySelector('input').value;

  if (tag_id != '')
    sendAjaxRequest('post', `/api/tag/follow/${tag_id}`, {}, tagFollowHandler);
    
  event.preventDefault();
}

function tagFollowHandler() {
  let follow = JSON.parse(this.responseText);
  let tag_id = follow['tag_id'];

  let button = document.getElementById(`follow-tag-${tag_id}`)

  button.onclick = sendUnFollowTagRequest

  button.id = `unFollow-tag-${tag_id}`
  button.classList.remove('follow-tag')
  button.classList.add('unFollow-tag')
  button.querySelector('i').innerHTML = 'done'
  button.querySelector('p').innerHTML = 'Following'
}

function sendUnFollowTagRequest(event) {
  let tag_id = event.currentTarget.querySelector('input').value;

  if (tag_id != '')
    sendAjaxRequest('delete', `/api/tag/unFollow/${tag_id}`, {}, tagUnFollowHandler);
    
  event.preventDefault();
}

function tagUnFollowHandler() {
  let follow = JSON.parse(this.responseText);
  let tag_id = follow['tag_id'];

  let button = document.getElementById(`unFollow-tag-${tag_id}`)

  button.onclick = sendFollowTagRequest

  button.id = `follow-tag-${tag_id}`
  button.classList.remove('unFollow-tag')
  button.classList.add('follow-tag')
  button.querySelector('i').innerHTML = 'add'
  button.querySelector('p').innerHTML = 'Follow'
}

/* Follow and un-follow questions */

function sendFollowQuestionRequest(event) {
  let question_id = event.currentTarget.querySelector('input[name=question]').value;

  if (question_id != '')
    sendAjaxRequest('post', `/api/question/follow/${question_id}`, {}, questionFollowHandler);
    
  event.preventDefault();
}

function questionFollowHandler() {
  let follow = JSON.parse(this.responseText);
  let question_id = follow['question_id'];

  let button = document.getElementById(`follow-question-${question_id}`)

  button.removeEventListener('click', sendFollowQuestionRequest)
  button.onclick = sendUnFollowQuestionRequest

  button.id = `un-follow-question-${question_id}`
  button.classList.remove('follow-question')
  button.classList.add('un-follow-question')
  button.innerHTML = `
    <input type="hidden" name="question" value="${question_id}">
    <i width="16" height="16" class="material-symbols-outlined ">done</i>
    Following`
}

function sendUnFollowQuestionRequest(event) {
  let question_id = event.currentTarget.querySelector('input[name=question]').value;

  if (question_id != '')
    sendAjaxRequest('delete', `/api/question/unFollow/${question_id}`, {}, questionUnFollowHandler);
    
  event.preventDefault();
}

function questionUnFollowHandler() {
  let follow = JSON.parse(this.responseText);
  let question_id = follow['question_id'];

  let button = document.getElementById(`un-follow-question-${question_id}`)

  button.removeEventListener('click', sendUnFollowQuestionRequest)
  button.onclick = sendFollowQuestionRequest

  button.id = `follow-question-${question_id}`
  button.classList.remove('un-follow-question')
  button.classList.add('follow-question')
  button.innerHTML = `
    <input type="hidden" name="question" value="${question_id}">
    <i width="16" height="16" class="material-symbols-outlined ">add</i>
    Follow`
}


/** Multi-select dropdown */

let options = document.querySelectorAll('option');
options.forEach(
  (option) => option.onmousedown = (e) => {
    e.preventDefault();
    if (e.target.hasAttribute('selected')) {
      e.target.removeAttribute('selected');
    }
    else {
      e.target.setAttribute('selected', '');
    }
  })

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

/*********** create answer comment ***********/
function answerCommentForm(event) {
  removeOpenedForms()

  let answer = event.target.parentElement.parentElement.parentElement
  let answer_card_id = answer.parentElement.id;
  answer.insertAdjacentElement('afterend', createAnswerCommentForm(answer_card_id))
}

function createAnswerCommentForm(answer_card_id) {
  let answer_card_id_list = answer_card_id.split('_', 2);
  let answer_id = answer_card_id_list[1]
  let previous_comment_form = document.querySelector(`.comment-answer-${answer_id}-form`)
  if(previous_comment_form!=null && previous_comment_form.innerHTML!='') return previous_comment_form;

  let comment_form = document.createElement('div');
  comment_form.className = 'card';
  comment_form.className = `comment-answer-${answer_id}-form`;
  comment_form.className = 'add-comment-form';
  setInnerHTML( comment_form,
    `
    <form method="POST" class="card-body m-0 p-0">
        <textarea class="w-100 h-100 m-0 border-0" placeholder="Type something..." rows="3"
            id="comment" name="comment" value="{{ old('comment') }}" required></textarea>
    </form>
    <div class=" card-footer">
    <div class = "row justify-content-between" style="padding: 0.75rem 0.75rem;">
        <button class="cancel-add-comment button-clear px-2 pr-3 pb-2 d-flex" style="margin: 0;" onclick="cancelCreateComment()">
            <input type="hidden" value="{{ $tag->tag_id }}">
            <p class="pb-2">Cancel</p>
        </button>
        <button id="add-comment-button" type="submit" onclick="sendCreateAnswerCommentRequest(${answer_id})" class="m-0">
            Comment
        </button>
        </div>
    </div>
    <script>
    var input = document.getElementById("comment");
    input.addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("add-comment-button").click();
      }
    });
    </script>
  `);
  return comment_form;
}

function sendCreateAnswerCommentRequest(answer_id) {
  let question_id = document.querySelector('#question_id').value;
  let comment = document.querySelector('#comment').value;

  if (comment != '')
    sendAjaxRequest('post', `/api/comment/` + question_id, { full_text: comment, question_id: question_id, answer_id: answer_id }, answerCommentAddedHandler);

  event.preventDefault();
}

function answerCommentAddedHandler() {
  let comment = JSON.parse(this.responseText);

  //delete comment form
  document.querySelector(`.add-comment-form`).innerHTML = '';

  // Create the new comment
  let new_comment = createComment(comment);

  // Insert the new comment
  let comments = document.querySelector(`.answer-${comment.answer_id}-comments`);
  if(comments!=null)
    comments.prepend(new_comment);
  else{
    comments = document.querySelector(`#answer_${comment.answer_id}`);
    comments.appendChild(new_comment);
  }
}

function createComment(comment) {
  let new_comment = document.createElement('div');
  new_comment.id = `comment_${comment.comment_id}`
  new_comment.className = 'border-top'
  new_comment.classList.add('d-flex')
  new_comment.classList.add('justify-content-between')
  new_comment.innerHTML = `
  <div class="modal fade" id="commentModal_${comment.comment_id}" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="commentModalLabel">Delete comment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="material-symbols-outlined">close</i>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this comment?
        </div>
        <div class="modal-footer border-0">
          <input type="hidden" name="comment_id" value="${comment.comment_id}">
          <button type="button" class="button-outline" data-dismiss="modal">Close</button>
          <button type="button" class="button delete-comment" data-dismiss="modal" onclick="sendDeleteCommentRequest(event)">Confirm</button>
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex flex-fill">
  <div class="d-flex align-items-center flex-column p-1">
      <button class="button-clear p-0 m-0 mr-2 update-votes-comment" type="button" onclick="sendUpdateVotesCommentRequest(event)">
          <input type="hidden" name="vote" value="1"></input>
          <input type="hidden" name="comment_id" value="${comment.comment_id}"></input>
          <i class="material-symbols-outlined">keyboard_arrow_up</i>
      </button>
      <p class="m-0 pr-2 text-nowrap" id="num-votes-comment-${comment.comment_id}">${comment.num_votes}</p>
      <button class="button-clear p-0 m-0 mr-2 update-votes-comment" type="button" onclick="sendUpdateVotesCommentRequest(event)">
          <input type="hidden" name="vote" value="-1"></input>
          <input type="hidden" name="comment_id" value="${comment.comment_id}"></input>
          <i class="material-symbols-outlined">keyboard_arrow_down</i>
      </button>
  </div>
  <div class="pt-3 flex-fill">
      <p class="m-0">
          <img src="/storage/${comment.author.picture_path}.jpeg" class="img-fluid rounded-circle" alt="user image" width="25px">
          <a href="url("/users/${comment.user_id}")">${comment.author.name}</a>
          ${comment.date}
      </p>
  <p class="card-text py-2">${comment.full_text}</p>
  </div>
</div>
<div class="ml-5 d-flex align-items-end flex-column">
    <div class="dropdown">
        <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
            <i class="material-symbols-outlined">more_vert</i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <data class="comment_id" hidden>${comment.comment_id}</data>
                <button class="dropdown-item edit_comment" type="button" onclick="editComment(event)">
                    <i width="16" height="16" class="material-symbols-outlined">edit</i>
                    Edit
                </button>
            <input type="hidden" name="comment_id" value="${comment.comment_id}">
            <button class="dropdown-item" type="button" data-toggle="modal" data-target="#commentModal_${comment.comment_id}">
                <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                Delete
            </button>
        </div>
    </div>
</div>
  `;
  addAnswerCard();
  return new_comment;
}

/*********** create question comment ***********/

function questionCommentForm(event) {
  let question = event.target.parentElement.parentElement.parentElement

  let question_id = document.querySelector('#question_id').value;
  
  removeOpenedForms()

  question.insertAdjacentElement('afterend', createQuestionCommentForm(question_id))
}

function createQuestionCommentForm(question_id) {
  //prevent duplicated comment form
  let previous_comment_form = document.querySelector('.add-comment-form')
  if(previous_comment_form!=null && previous_comment_form.innerHTML!='') return previous_comment_form;
  if(previous_comment_form!=null) previous_comment_form.remove()

  let comment_form = document.createElement('div')
  comment_form.className = 'card'
  comment_form.classList.add('add-comment-form')
  comment_form.innerHTML = `
    <input type="hidden" name="question_id" id="question_id" value="${question_id}"></input>
    <form method="POST" class="card-body m-0 p-0">
        <textarea class="w-100 h-100 m-0 border-0" placeholder="Type something..." rows="5"
            id="comment" name="comment" value="{{ old('comment') }}" required></textarea>
    </form>
    <div class=" card-footer">
    <div class = "row justify-content-between" style="padding: 0.75rem 0.75rem;">
        <button class="cancel-add-comment button-clear px-2 pr-3 pb-2 d-flex" style="margin: 0;" onclick="cancelCreateComment()">
            <input type="hidden" value="{{ $tag->tag_id }}">
            <p class="pb-2">Cancel</p>
        </button>
        <button id="add-comment-button" type="submit" style="margin: 0;" onclick="sendCreateQuestionCommentRequest()" class="m-0">
            Comment
        </button>
        </div>
    </div>
  `;
  return comment_form;
}

function sendCreateQuestionCommentRequest() {
  let question_id = document.querySelector('#question_id').value;
  let comment = document.querySelector('#comment').value;

  if (comment != '')
    sendAjaxRequest('post', `/api/comment/` + question_id, { full_text: comment, question_id: question_id}, questionCommentAddedHandler);

  event.preventDefault();
}

function  questionCommentAddedHandler() {
  let comment = JSON.parse(this.responseText);

  //delete comment form
  document.querySelector('.add-comment-form').remove()

  // Create the new comment
  let new_comment = createComment(comment);

  // Insert the new comment
  let comments = document.querySelector(`.question-comments`);
  comments.prepend(new_comment);

  addAnswerCard()
}

function cancelCreateComment(){
  let commentForm = document.querySelector('.add-comment-form')
  commentForm.remove()

  // Insert answer form back
  addAnswerCard();
}

function addAnswerCard() {
  let question_id = document.querySelector('#question_id').value;
  let add_answer_card = document.querySelector('#add-answer-card');
  setInnerHTML( add_answer_card, `
  <form method="POST" class="card-body m-0 p-0">
    <input type="hidden" name="question_id" id="question_id" value="${question_id}"></input>
    <textarea class="w-100 h-100 m-0 border-0" placeholder="Type something..." rows="5"
      id="answer" name="answer" value="{{ old('answer') }}" required></textarea>
  </form>
<div class="card-footer text-right">
  <button id="add-answer-button" type="submit" onclick="sendCreateAnswerRequest(event)" class="m-0">
      Answer
  </button>
</div>
<script>
var input = document.getElementById("answer");
input.addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    document.getElementById("add-answer-button").click();
  }
});
</script>
  `
  )
}

function cancelCreateComment(){
  let question_id = document.querySelector('#question_id').value;

  let commentForm = document.querySelector('.add-comment-form')
  commentForm.remove()

    // Insert answer form back
    let add_answer_card = document.querySelector('#add-answer-card');
    add_answer_card.innerHTML = `
    <form method="POST" class="card-body m-0 p-0">
      <input type="hidden" name="question_id" id="question_id" value="${question_id}"></input>
      <textarea class="w-100 h-100 m-0 border-0" placeholder="Type something..." rows="5"
        id="answer" name="answer" value="{{ old('answer') }}" required></textarea>
    </form>
  <div class="card-footer text-right">
    <button id="add-answer-button" type="submit" onclick="sendCreateAnswerRequest(event)" class="m-0">
        Answer
    </button>
  </div>
    `
}

/*********** delete an comment ***********/

function sendDeleteCommentRequest(event) {
  let comment_id = event.target.parentElement.children[0].value;

  sendAjaxRequest('delete', '/api/comment/delete/' + comment_id, {}, commentDeletedHandler);
  event.preventDefault();
}

function commentDeletedHandler() {

  let deletedComment = JSON.parse(this.responseText);

  let deletedCommentElement = document.getElementById("comment_" + deletedComment.comment_id)
  deletedCommentElement.remove();
}
/*********** edit comment ***********/

function editComment(event) {
  removeOpenedForms()

  let comment_id = event.target.parentElement.children[0].innerText;
  let comment = document.querySelector('#comment_' + comment_id);

  let text = comment.querySelector('.card-text').innerText;
  let text_card = comment.querySelector('.card-text');
  text_card.insertAdjacentElement("afterend", createCommentForm(comment_id, text));
  text_card.innerHTML = '';
}

function createCommentForm(comment_id, text) {
  let comment_form = document.createElement('div');
  let comment = document.getElementById(comment_id);

  // prevent duplicated edit form
  let previous_comment_form = document.querySelector(`#comment_form_${comment_id}`)
  if(previous_comment_form!=null&&previous_comment_form.innerHTML!='') return previous_comment_form;

  comment_form.classList.add('comment-form')
  comment_form.classList.add('py-2')
  comment_form.classList.add('w-100')
  comment_form.id = `comment_form_${comment_id}`

  setInnerHTML(comment_form,
    `
    <input type="hidden" name="comment_id" id="comment_id" value="${comment_id}"></input>
    <input type="hidden" name="comment" id="comment" value="${comment}"></input>
    <textarea id="full_text" rows="4" type="text" name="full_text" class="edit-text mt-2" required/>${text}</textarea>
    <div class = "row justify-content-between" style="padding: 0.75rem 0.75rem;">
        <button class="cancel-add-comment button-clear px-2 pr-3 pb-2 d-flex" style="margin: 0;" onclick="cancelEditComment(${comment_id},'${text}')">
            <p class="pb-2">Cancel</p>
        </button>
        <button id="update-comment-button" onclick="commentUpdater(event)" type="submit" class="m-0">
            Save Changes
        </button>
    </div>
      <script>
      var input = document.getElementById("full_text");
      input.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
          event.preventDefault();
          document.getElementById("update-comment-button").click();
        }
      });
      </script>
`)
  return comment_form;
}

function cancelEditComment(comment_id, text) {
  // Insert answer form back
  addAnswerCard();

  let p = document.createElement('p');
  p.classList.add('card-text', 'pb-5', 'pt-2');
  p.innerText = text;

  let comment_element = document.querySelector('#comment_' + comment_id);
  let comment_form = comment_element.querySelector('.comment-form');
  comment_form.parentElement.querySelector('.card-text').appendChild(p);
  comment_form.remove();
}


function commentUpdater() {
  let new_text = document.querySelector('#full_text').value;
  let comment_id = document.querySelector('#comment_id').value;
  sendAjaxRequest('put', '/api/comment/update/' + comment_id, { full_text: new_text }, sendCreateCommentUpdateRequest);
}


function sendCreateCommentUpdateRequest() {
  let comment = JSON.parse(this.responseText);

  let p = document.createElement('p');
  p.classList.add('card-text', 'pb-5', 'pt-2');
  p.innerText = comment.full_text;

  let em = document.createElement('em')
  em.innerText = 'edited'

  let comment_element = document.querySelector('#comment_' + comment.comment_id);
  let comment_form = comment_element.querySelector('.comment-form');
  comment_form.parentElement.querySelector('.card-text').appendChild(p);

  comment_head = comment_form.parentElement.children[0]
  if (comment_head.lastElementChild.tagName != 'EM')
    comment_head.appendChild(em)
  comment_form.remove();
  addAnswerCard();

}

function cancelEditComment(comment_id, text) {
  addAnswerCard();

  let p = document.createElement('p');
  p.classList.add('card-text', 'pb-5', 'pt-2');
  p.innerText = text;

  let comment_element = document.querySelector('#comment_' + comment_id);
  let comment_form = comment_element.querySelector('.comment-form');
  comment_form.parentElement.querySelector('.card-text').appendChild(p);
  comment_form.remove();
}

function removeOpenedForms(){
  if(document.querySelector('.answer-form')!=null){
    let answer_id = document.querySelector('#answer_id').value
    let text = document.querySelector('#full_text').textContent
    cancelEditAnswer(answer_id, text)
  }
  if(document.querySelector('.comment-form')!=null){
    let comment_id = document.querySelector('#comment_id').value
    let text = document.querySelector('#full_text').textContent
    cancelEditComment(comment_id, text)
  }
  if(document.querySelector('.add-comment-form')!=null) cancelCreateComment()
  document.querySelector('#add-answer-card').innerHTML = '';
}

function submitQuestionUpdate(){
  document.getElementById("edit-question-form").submit();
}
function updateNotification(notification_id){
  let notification_button = document.getElementById("button-notification-" + notification_id)
  let red_circle = notification_button.getElementsByTagName("span")[0]
  if(!red_circle) return
  notification_button.removeChild(red_circle)

  let num_notifications_span = document.getElementById("num-notifications")
  let num = parseInt(num_notifications_span.textContent) - 1
  if(num === 0) {
    num_notifications_span.textContent = ""
  }
  else num_notifications_span.textContent = num
}


function sendUpdateNotificationRequest(event) {
  let button_id
  if(event.target.className === "btn bg-transparent shadow-none border-0 d-flex justify-content-between align-items-center w-100 button-notification"){
    button_id = event.target.id
  }
  if(event.target.className === "d-flex flex-column" || event.target.className === "material-icons ml-4 red-circle-notification"){
    button_id = event.target.parentElement.id
  }
  if(event.target.className === "text-left" || event.target.className === "h5 text-left"){
    button_id = event.target.parentElement.parentElement.id
  }
  let notification_id = button_id.split('-').pop()
  if (notification_id != '')
    sendAjaxRequest('post', 
                    '/api/notification/update/' + notification_id, 
                    {}, 
                    function(){return updateNotification(notification_id);})
  event.stopPropagation()
  event.preventDefault()
}

function redirect_notification(notification_id){
  window.location.assign('/notification/' + notification_id);
}

function sendUpdateVotesRequest(event) {
  id = event.currentTarget.querySelectorAll('input')[1].value;
  value = event.currentTarget.querySelectorAll('input')[0].value;

  if (id != '')
    sendAjaxRequest('post', `/api/question/${id}/vote`, {question_id : id, vote: value}, sendUpdateVotesHandler);
  event.preventDefault();
}

function sendUpdateVotesHandler() {
  let response = JSON.parse(this.responseText);
  console.log(response);
  let votes = response.num_votes;
  let question_id = response.question_id;
  let votesHTML = document.getElementById('num-votes-' + question_id);

  votesHTML.innerHTML = votes;
}

function sendUpdateVotesAnswerHandler() {
  console.log(this.responseText);
  let response = JSON.parse(this.responseText);
  console.log(response);
  let votes = response.num_votes;
  let answer_id = response.answer_id;
  let votesHTML = document.getElementById('num-votes-answer-' + answer_id);

  votesHTML.innerHTML = votes;
}

function sendUpdateVotesAnswerRequest(event) {
  id = event.currentTarget.querySelectorAll('input')[1].value;
  value = event.currentTarget.querySelectorAll('input')[0].value;
  console.log(id);
  console.log(value);
  if (id != '')
    sendAjaxRequest('post', `/api/answer/${id}/vote`, {answer_id : id, vote: value}, sendUpdateVotesAnswerHandler);
  event.preventDefault();
}

function sendUpdateVotesCommentHandler() {
  console.log(this.responseText);
  let response = JSON.parse(this.responseText);
  console.log(response);
  let votes = response.num_votes;
  let comment_id = response.comment_id;
  let votesHTML = document.getElementById('num-votes-comment-' + comment_id);

  votesHTML.innerHTML = votes;
}

function sendUpdateVotesCommentRequest(event) {
  id = event.currentTarget.querySelectorAll('input')[1].value;
  value = event.currentTarget.querySelectorAll('input')[0].value;
  console.log(id);
  console.log(value);
  if (id != '')
    sendAjaxRequest('post', `/api/comment/${id}/vote`, {comment_id : id, vote: value}, sendUpdateVotesCommentHandler);
  event.preventDefault();
}

/***********  ***********/

function removeOpenedForms(){
  if(document.querySelector('.answer-form')!=null){
    let answer_id = document.querySelector('#answer_id').value
    let text = document.querySelector('#full_text').textContent
    cancelEditAnswer(answer_id, text)
  }
  if(document.querySelector('.comment-form')!=null){
    let comment_id = document.querySelector('#comment_id').value
    let text = document.querySelector('#full_text').textContent
    cancelEditComment(comment_id, text)
  }
  if(document.querySelector('.add-comment-form')!=null) cancelCreateComment()
  document.querySelector('#add-answer-card').innerHTML = '';
}

function submitQuestionUpdate(){
  document.getElementById("edit-question-form").submit();
}

/*********** delete an account ***********/

function sendDeleteUserRequest(event) {
  let user_id = event.target.parentElement.children[0].value;
  sendAjaxRequest('put', '/api/user/delete/' + user_id, {user_id : user_id}, logout);
  event.preventDefault();

  sendDisableUserRequest(user_id);
}

function sendDisableUserRequest(user_id) {
  sendAjaxRequest('post', '/api/disable/create/' + user_id, {user_id : user_id}, () => {} );

}

function logout(){
  window.location.href = "/";
}

/***********  ***********/

addEventListeners();