<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store() { return response()->json(['message' => 'created'], 201); }
    public function show($article) { return response()->json(['data' => null]); }
    public function update($article) { return response()->json(['message' => 'updated']); }
    public function destroy($article) { return response()->json(['message' => 'deleted']); }
    public function publish($article) { return response()->json(['message' => 'published']); }
    public function schedule($article) { return response()->json(['message' => 'scheduled']); }
    public function autosave($article) { return response()->json(['message' => 'autosaved']); }
}
