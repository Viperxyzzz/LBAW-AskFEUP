<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Block;

class BlockController extends Controller
{
    //

    /**
     * Store a newly created block.
     *
     * @param  \Illuminate\Http\Request  $request HTTP Post Request.
     * @return \Illuminate\Http\Response Block json object.
     */
    public function store(Request $request, $user_id)
    {
        if(!Auth::check()) return redirect('/login');
        $this->authorize('create', Block::class);

        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $block = new Block;
        $block->user_id = $user_id;
        $block->reason = $request->reason;
        $block->date = date('Y-m-d H:i:s');

        $block->save();

        return $block;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::check()) return redirect('/login');
        $this->authorize('create', Block::class);
        $block = Block::where('user_id', $id)->first();
        $block->delete();
        return $block;
    }
}
