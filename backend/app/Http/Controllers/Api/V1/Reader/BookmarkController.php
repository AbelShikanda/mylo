<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;

class BookmarkController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store($article) { return response()->json(['message' => 'bookmarked']); }
    public function destroy($article) { return response()->json(['message' => 'unbookmarked']); }
}
