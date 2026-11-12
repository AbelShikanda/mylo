<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('notifications.manage'), 403);
        return response()->json(['data' => []]);
    }
}
