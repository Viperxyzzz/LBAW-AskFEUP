<div class="card my-5">
    <div class="card-body">
        <a class="card-title font-weight-bold h5">{{ $question->title }}</a>
        <p class="card-text">{{ $question->full_text }}</p>
        <div class="flex">
            <span class="badge bg-secondary">todo</span>
        </div>
        <a class="btn btn-primary text-end" href="#">TODO</a>
    </div>
    <div class="card-footer text-center">
        {{ $question->date }}
    </div>

</div>