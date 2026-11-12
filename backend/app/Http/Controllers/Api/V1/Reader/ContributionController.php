<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;

class ContributionController extends Controller
{
    public function index() { return response()->json(['data' => []]); }
    public function store() { return response()->json(['message' => 'contributed']); }
}
