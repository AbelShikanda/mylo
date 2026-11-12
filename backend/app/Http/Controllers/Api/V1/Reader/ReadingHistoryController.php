<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;

class ReadingHistoryController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}
