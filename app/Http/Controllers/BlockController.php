<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Block;

class BlockController extends Controller
{
    /**
     * Create a user block.
     * @param Request $request 
     * @param mixed $user_id Id of the user to be blocked.
     * @return mixed JSON of the newly created block.
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
     * Remove a user block.
     *
     * @param  int  $id Id of the block to be removed
     * @return mixed JSON of the destroyed block entry
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
