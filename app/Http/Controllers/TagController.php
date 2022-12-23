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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all();
        $topics = Topic::all();
        return view('pages.tags', ['tags' => $tags, 'topics' => $topics]);
    }

    public function search(Request $request) {
        $search =  $request->input('search') ?? '';
        $topics = $request->input('topics') ?? [];
        $tags = Tag::search($search, $topics);
        if (Auth::check()) {
            foreach($tags as $tag) {
                $tag['following'] = Auth::user()->follows_tag($tag->tag_id);
            }
        }
        return $tags;
    }

    public function follow(Request $request, $tag_id) {
        if (!Auth::check()) abort(403);
        if ($tag_id == NULL) return;
        return UserTag::follow(Auth::id(), $tag_id);
    }

    public function unFollow(Request $request, $tag_id) {
        $follow = UserTag::where([
            ['user_id', '=', Auth::id()],
            ['tag_id', '=', $tag_id]
        ]);
        $follow->delete();
        return ['tag_id' => $tag_id];
    }

    /**
     * Get a validator for an incoming create tag request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'tag_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'topic' => 'required|number'
        ]);
    }


    /**
     * Create a new tag.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!Auth::check()) return redirect('/login');
        $this->authorize('create', Tag::class);
        $tag = new Tag;
        $tag->tag_name = $request->name;
        $tag->tag_description = $request->description;
        $tag->topic_id = $request->topic;

        $tag->save();

        return redirect('/tags');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
