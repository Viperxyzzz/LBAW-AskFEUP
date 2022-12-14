<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{

    public function update($notification_id) {
      if (!Auth::check()) return redirect('/login');
      $notification = Notification::find($notification_id);
      Gate::authorize('update', $notification);
      $notification->update(['viewed' => 'Yes']);
      return $notification;
    }
}
