<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'user_id' => 'required|int',
            'reason' => 'required|string|max:255'
        ]);

        $block = new Block;
        $block->user_id = $user_id;
        $block->reason = $request->reason;
        $block->date = date('Y-m-d H:i:s');

        $block->save();

        return $block;
    }
}
