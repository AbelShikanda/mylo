<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function show() { return response()->json(['data' => []]); }
    public function addItem() { return response()->json(['message' => 'added']); }
    public function removeItem($item) { return response()->json(['message' => 'removed']); }
}
