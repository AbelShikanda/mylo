<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;

class DraftController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
}
