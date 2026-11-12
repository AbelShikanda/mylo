<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function show($order) { return response()->json(['data' => null]); }
}
