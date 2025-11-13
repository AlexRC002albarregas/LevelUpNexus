<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Devuelve la cantidad de notificaciones sin leer del usuario.
     */
    public function count()
    {
        $count = auth()->user()->notificationsCount();
        return response()->json(['count' => $count]);
    }
}

