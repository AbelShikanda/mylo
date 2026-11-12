<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store() { return response()->json(['message' => 'uploaded'], 201); }
    public function destroy($media) { return response()->json(['message' => 'deleted']); }
}
