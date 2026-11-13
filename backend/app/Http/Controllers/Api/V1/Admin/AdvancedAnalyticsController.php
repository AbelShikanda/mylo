<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Administration\AdvancedAnalyticsService;
use Illuminate\Http\Request;

class AdvancedAnalyticsController extends Controller
{
    protected AdvancedAnalyticsService $analyticsService;

    public function __construct(AdvancedAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function registrationTrends(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getRegistrationTrends($range);

        return response()->json(['data' => $data]);
    }

    public function readerEngagement(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getReaderEngagement($range);

        return response()->json(['data' => $data]);
    }

    public function writerActivity(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getWriterActivity($range);

        return response()->json(['data' => $data]);
    }

    public function articlePerformance(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getArticlePerformance($range);

        return response()->json(['data' => $data]);
    }

    public function categoryPopularity(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $data = $this->analyticsService->getCategoryPopularity();

        return response()->json(['data' => $data]);
    }

    public function searchActivity(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getSearchActivity($range);

        return response()->json(['data' => $data]);
    }

    public function bookSales(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getBookSales($range);

        return response()->json(['data' => $data]);
    }

    public function financialContributions(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getFinancialContributions($range);

        return response()->json(['data' => $data]);
    }

    public function payouts(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getPayouts($range);

        return response()->json(['data' => $data]);
    }

    public function failedTransactions(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getFailedTransactions($range);

        return response()->json(['data' => $data]);
    }

    public function moderationActivity(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $range = $request->get('range', '30d');
        $data = $this->analyticsService->getModerationActivity($range);

        return response()->json(['data' => $data]);
    }

    public function platformOverview(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $data = $this->analyticsService->getPlatformOverview();

        return response()->json(['data' => $data]);
    }

    public function reports(Request $request)
    {
        abort_unless($request->user()->hasPermission('analytics.view'), 403);

        $data = $this->analyticsService->getPlatformOverview();

        return response()->json(['data' => $data]);
    }
}
