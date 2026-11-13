<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Administration\AdvancedAnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AdvancedAnalyticsService $analyticsService;

    public function __construct(AdvancedAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function reports(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        return response()->json(['data' => $this->analyticsService->getPlatformOverview()]);
    }
}
