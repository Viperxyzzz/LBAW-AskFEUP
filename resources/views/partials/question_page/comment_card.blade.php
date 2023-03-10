<div id="comment_{{ $comment->comment_id }}" class="border-top d-flex justify-content-between">
    @include('partials.question_page.delete_comment_modal', ['comment' => $comment])
    <div class="d-flex flex-fill">
        <div class="d-flex align-items-center flex-column p-1">
            <button class="button-clear p-0 m-0 mr-2 update-votes-comment" type="button">
                <input type="hidden" name="vote" value="1"></input>
                <input type="hidden" name="comment_id" value="{{$comment->comment_id}}"></input>
                @if($comment->vote()!=null && $comment->vote()==1)
                <i id="up-comment-{{$comment->comment_id}}-vote" class="material-symbols-outlined voted rounded-circle">keyboard_arrow_up</i>
                @else
                <i id="up-comment-{{$comment->comment_id}}-vote" class="material-symbols-outlined rounded-circle">keyboard_arrow_up</i>
                @endif
            </button>
            <p class="m-0 pr-2 text-nowrap" id="num-votes-comment-{{$comment->comment_id}}">{{ $comment->num_votes }}</p>
            <button class="button-clear d-block p-0 m-0 mr-2 update-votes-comment" type="button">
                <input type="hidden" name="vote" value="-1"></input>
                <input type="hidden" name="comment_id" value="{{$comment->comment_id}}"></input>
                @if($comment->vote()!=null && $comment->vote()==-1)
                <i id="down-comment-{{$comment->comment_id}}-vote" class="material-symbols-outlined voted rounded-circle">keyboard_arrow_down</i>
                @else
                <i id="down-comment-{{$comment->comment_id}}-vote" class="material-symbols-outlined rounded-circle">keyboard_arrow_down</i>
                @endif
            </button>
        </div>
        <div class="pt-3 flex-fill">
            <p class="m-0">
                <img src="{{asset('storage/'.($comment->author->picture_path).'.jpeg')}}" class="img-fluid rounded-circle keep-ratio" alt="user image" width="25px">
                @if($comment->is_accessible_user())
                <a href="{{ url("/users/$comment->user_id") }}"> {{ $comment->author->name }}</a>
                @else
                <a> {{ $comment->author->name }}</a>
                @endif
                {{ \Carbon\Carbon::parse($comment->date)->format('d/m/Y')}}
                @if($comment->was_edited)
                <em>edited</em>
                @endif
            </p>
        <p class="card-text py-2">{{ $comment->full_text }}</p>
        </div>
    </div>
    @include('partials.admin.add_report')
        <div class="ml-5 d-flex align-items-end flex-column">
            @can('edit', $comment)
                <div class="dropdown">
                    <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true"">
                        <i class="material-symbols-outlined">more_vert</i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <data class="comment_id" hidden>{{ $comment->comment_id }}</data>
                            <button class="dropdown-item edit_comment" type="button">
                                <i width="16" height="16" class="material-symbols-outlined ">edit</i>
                                Edit
                            </button>
                        <input type="hidden" name="comment_id" value="{{$comment->comment_id}}">
                        <button class="dropdown-item" type="button" data-toggle="modal" data-target="#commentModal_{{$comment->comment_id}}">
                            <i width="16" height="16" class="material-symbols-outlined ">delete</i>
                            Delete
                        </button>
                        <button class="dropdown-item m-0" type="button" data-toggle="modal" 
                            data-target="#add-report-modal-{{ $question->question_id }}-{{ $comment->answer_id }}-{{ $comment->comment_id }}">
                            <i width="16" height="16" class="material-symbols-outlined ">flag</i>
                            Report
                        </button>
                    </div>
                </div>
            @endcan
        </div>
</div>