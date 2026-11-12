<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function markAsRead($notification) { return response()->json(['message' => 'read']); }
}
