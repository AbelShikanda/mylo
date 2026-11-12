<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function search() { return response()->json(['data' => []]); }
}
