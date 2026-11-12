<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function show($slug) { return response()->json(['data' => null]); }
    public function like($article) { return response()->json(['message' => 'liked']); }
    public function unlike($article) { return response()->json(['message' => 'unliked']); }
    public function comments($article) { return response()->json(['data' => []]); }
    public function addComment($article) { return response()->json(['message' => 'comment added']); }
    public function report($article) { return response()->json(['message' => 'reported']); }
    public function related($article) { return response()->json(['data' => []]); }
}

class CategoryController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function show($slug) { return response()->json(['data' => null]); }
    public function articles($category) { return response()->json(['data' => []]); }
}

class TagController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function show($slug) { return response()->json(['data' => null]); }
}

class SearchController extends Controller
{
    public function search() { return response()->json(['data' => []]); }
}

class BookController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function show($slug) { return response()->json(['data' => null]); }
}

class CartController extends Controller
{
    public function show() { return response()->json(['data' => []]); }
    public function addItem() { return response()->json(['message' => 'added']); }
    public function removeItem($item) { return response()->json(['message' => 'removed']); }
}

class CheckoutController extends Controller
{
    public function checkout() { return response()->json(['message' => 'checkout']); }
}
