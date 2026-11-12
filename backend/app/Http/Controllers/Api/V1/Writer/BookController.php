<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store() { return response()->json(['message' => 'created'], 201); }
    public function update($book) { return response()->json(['message' => 'updated']); }
}
