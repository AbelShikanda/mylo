<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;

class PreferenceController extends Controller
{
    public function show() { return response()->json(['data' => []]); }
    public function update() { return response()->json(['message' => 'updated']); }
}

class BookmarkController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store($article) { return response()->json(['message' => 'bookmarked']); }
    public function destroy($article) { return response()->json(['message' => 'unbookmarked']); }
}

class ReadingHistoryController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}

class NotificationController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function markAsRead($notification) { return response()->json(['message' => 'read']); }
}

class ContributionController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store() { return response()->json(['message' => 'contributed']); }
}

class LibraryController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}

class OrderController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function show($order) { return response()->json(['data' => null]); }
}
