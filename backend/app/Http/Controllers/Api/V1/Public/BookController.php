<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function show($slug) { return response()->json(['data' => null]); }
}
