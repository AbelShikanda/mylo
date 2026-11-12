<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function update($comment) { return response()->json(['message' => 'updated']); }
}
