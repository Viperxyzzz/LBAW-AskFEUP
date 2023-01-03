<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\UserBadgeSupport;

class BadgeController extends Controller
{
    /**
     * Add a user supporting badge entry.
     * @param Request $request Request with badge, user achiever and user supporter id's.
     * @return UserBadgeSupport|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function support(Request $request) {
        if (!Auth::check()) return redirect('/feed');

        $request->validate([
            'badge_id' => 'required',
            'user_id' => 'required'
        ]);

        $support = new UserBadgeSupport;
        $support->user_who_supports = Auth::id();
        $support->user_who_achieves = (int)$request->get('user_id');
        $support->badge_id = (int)$request->get('badge_id');

        $support->save();

        return $support;
    }

    /**
     * Remove a user supporting badge entry.
     * @param Request $request Request with badge, user achiever and user supporter id's.
     * @return UserBadgeSupport|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function unsupport(Request $request) {
        if (!Auth::check()) return redirect('/feed');

        $request->validate([
            'badge_id' => 'required',
            'user_id' => 'required'
        ]);

        $support = UserBadgeSupport::where([
            ['badge_id', $request->badge_id],
            ['user_who_achieves', $request->user_id],
            ['user_who_supports', Auth::id()],
        ]);

        if ($support->exists())
            $support->delete();

        return $support->first();
    }
}
