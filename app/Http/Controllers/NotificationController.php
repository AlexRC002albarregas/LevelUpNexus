<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get the count of unread notifications.
     */
    public function count()
    {
        $count = auth()->user()->notificationsCount();
        return response()->json(['count' => $count]);
    }
}

