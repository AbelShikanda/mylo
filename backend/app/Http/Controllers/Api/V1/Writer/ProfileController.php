<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show() { return response()->json(['data' => []]); }
    public function update() { return response()->json(['message' => 'updated']); }
}

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

class DraftController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}

class ScheduledArticleController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}

class MediaController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store() { return response()->json(['message' => 'uploaded'], 201); }
    public function destroy($media) { return response()->json(['message' => 'deleted']); }
}

class BookController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store() { return response()->json(['message' => 'created'], 201); }
    public function update($book) { return response()->json(['message' => 'updated']); }
}

class CommentController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function update($comment) { return response()->json(['message' => 'updated']); }
}

class FinancialSupportController extends Controller
{
    public function activate() { return response()->json(['message' => 'activated']); }
    public function deactivate() { return response()->json(['message' => 'deactivated']); }
}

class EarningsController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}

class AnalyticsController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}
