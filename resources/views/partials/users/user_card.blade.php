<div class="card d-flex flex-row m-3 p-2 bg-light" style="width: 250px;">
    <div class="align-self-center">
      <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="img-fluid rounded-start" alt="user image" width="60px">
    </div>
    <div class="card-body mx-2 p-2">
        <h4 class="card-title m-0 p-0">
            <a href="#">{{ $user->username }}</a>
        </h4>
        <p class="card-body m-0 p-0">{{ $user->name }}</p>
        <p class="card-body m-0 p-0">{{ $user->score }} points</p>
    </div>
</div>