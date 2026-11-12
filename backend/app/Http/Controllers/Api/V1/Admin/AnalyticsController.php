<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AnalyticsController extends Controller
{
    public function reports(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);
        return response()->json(['data' => []]);
    }
}
