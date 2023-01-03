<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Tag;
use App\Models\Topic;
use App\Models\UserTag;

class TagController extends Controller
{

    /**
     * Display a listing of all the tags available.
     *
     * @return mixed Displays the tag search page.
     */
    public function index()
    {
        $tags = Tag::orderBy('tag_id')->get();
        $topics = Topic::all();
        return view('pages.tags', ['tags' => $tags, 'topics' => $topics]);
    }

    /**
     * Search for tags using the available filters: topic and text search.
     * 
     * @param Request $request Request with topics and search params.
     * @return array Returns JSON with filtered tags and all available topics.
     */
    public function search(Request $request) {
        $search =  $request->input('search') ?? '';
        $topics = $request->input('topics') ?? [];
        $tags = Tag::search($search, $topics);
        if (Auth::check()) {
            foreach($tags as $tag) {
                $tag['following'] = Auth::user()->follows_tag($tag->tag_id);
                $tag['manage'] = Auth::user()->can('manage', Tag::class);
            }
        }
        return ['tags' => $tags, 'topics' => Topic::all()];
    }

    /**
     * Follow a tag.
     * 
     * @param Request $request
     * @param mixed $tag_id Id of the tag to be followed.
     * @return UserTag JSON with newly created following relation.
     */
    public function follow(Request $request, $tag_id) {
        if (!Auth::check()) abort(403);
        if ($tag_id == NULL) return;
        return UserTag::follow(Auth::id(), $tag_id);
    }

    /**
     * Unfollow a tag.
     * 
     * @param Request $request
     * @param mixed $tag_id Id of the tag to be un-followed.
     * @return array JSON with the unfollowed tag id.
     */
    public function unFollow(Request $request, $tag_id) {
        $follow = UserTag::where([
            ['user_id', '=', Auth::id()],
            ['tag_id', '=', $tag_id]
        ]);
        $follow->delete();
        return ['tag_id' => $tag_id];
    }


    /**
     * Store a newly created tag in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array JSON with tag and all topics.
     */
    public function store(Request $request)
    {
        if(!Auth::check()) return redirect('/login');
        $this->authorize('create', Tag::class);

        $request->validate([
            'name' => 'required|string|max:20',
            'description' => 'required|string|max:255',
            'topic' => 'int'
        ]);

        $tag = new Tag;
        $tag->tag_name = $request->input('name');
        $tag->tag_description = $request->input('description');
        $tag->topic_id = $request->input('topic');

        $tag->save();

        $tag['manage'] = Auth::user()->can('manage', Tag::class);

        return ['tag' => $tag, 'topics' => Topic::all()];
    }

    /**
     * Edit a tag from storage.
     *
     * @param  \Illuminate\Http\Request  $request POST request with all new tag info.
     * @param  int  $id If of the tag to be edited.
     * @return mixed JSON of newly edited tag.
     */
    public function update(Request $request, $id)
    {
        if(!Auth::check()) return redirect('/login');
        $this->authorize('manage', Tag::class);

        $request->validate([
            'name' => 'required|string|max:20',
            'description' => 'required|string|max:255',
            'topic' => 'int'
        ]);
        
        $tag = Tag::find($id);
        $tag->tag_name = $request->input('name');
        $tag->tag_description = $request->input('description');
        $tag->topic_id = $request->input('topic');

        $tag->save();

        return $tag;
    }

    /**
     * Remove a tag from storage
     *
     * @param  int  $id Id of the tag to be removed.
     * @return mixed
     */
    public function destroy($id)
    {
      if(!Auth::check()) return redirect('/login');
      $this->authorize('manage', Tag::class);
      $tag = Tag::find($id);
      $tag->delete();
      return $tag;
    }
}
