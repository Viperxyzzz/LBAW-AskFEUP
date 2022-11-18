<div class="col-lg-8 m-0 p-0">
    <div class="card mb-4 border-0">
        <div class="card-body text-center">
        <h1>{{$user->name}}</h1>
        <h3>Score in the community: <strong>{{$user->score}}</strong></h3>
        </div>
    </div>
    <div class="d-flex justify-content-between pt-5 ml-5 pl-5">
        <h2 class="m-0">Summary</h2>
        <h3 class="m-0">{{$user->email}}</h3>
    </div>
</div>