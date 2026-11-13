<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Administrator;
use App\Services\Administration\AdvancedModerationService;
use Illuminate\Http\Request;

class AdvancedModerationController extends Controller
{
    protected AdvancedModerationService $moderationService;

    public function __construct(AdvancedModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    public function getReports(Request $request)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        $filters = $request->only(['status', 'reportable_type', 'search']);
        $reports = $this->moderationService->getReportedContent($filters);

        return response()->json([
            'data' => $reports->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ],
            ],
        ]);
    }

    public function resolveReport(Request $request, string $reportId)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        /** @var Administrator $moderator */
        $moderator = $request->user();

        $validated = $request->validate([
            'action' => 'required|in:approved,rejected,pending',
            'reason' => 'required|string|max:1000',
        ]);

        $this->moderationService->resolveReport($reportId, $validated['action'], $validated['reason'], $moderator);

        return response()->json(['message' => 'Report resolved']);
    }

    public function getModerationRecords(Request $request)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        $filters = $request->only(['moderator_id', 'action', 'subject_type']);
        $records = $this->moderationService->getModerationRecords($filters);

        return response()->json([
            'data' => $records->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $records->currentPage(),
                    'last_page' => $records->lastPage(),
                    'per_page' => $records->perPage(),
                    'total' => $records->total(),
                ],
            ],
        ]);
    }

    public function suspendAccount(Request $request)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        /** @var Administrator $moderator */
        $moderator = $request->user();

        $validated = $request->validate([
            'account_type' => 'required|in:reader,writer',
            'account_id' => 'required|uuid',
            'reason' => 'required|string|max:1000',
        ]);

        $this->moderationService->suspendAccount(
            $validated['account_type'],
            $validated['account_id'],
            $validated['reason'],
            $moderator
        );

        return response()->json(['message' => 'Account suspended']);
    }

    public function reinstateAccount(Request $request)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        /** @var Administrator $moderator */
        $moderator = $request->user();

        $validated = $request->validate([
            'account_type' => 'required|in:reader,writer',
            'account_id' => 'required|uuid',
            'reason' => 'required|string|max:1000',
        ]);

        $this->moderationService->reinstateAccount(
            $validated['account_type'],
            $validated['account_id'],
            $validated['reason'],
            $moderator
        );

        return response()->json(['message' => 'Account reinstated']);
    }

    public function removeComment(Request $request, string $commentId)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        /** @var Administrator $moderator */
        $moderator = $request->user();

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->moderationService->removeComment($commentId, $validated['reason'], $moderator);

        return response()->json(['message' => 'Comment removed']);
    }

    public function restrictAccount(Request $request)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        /** @var Administrator $moderator */
        $moderator = $request->user();

        $validated = $request->validate([
            'account_type' => 'required|in:reader,writer',
            'account_id' => 'required|uuid',
            'restrictions' => 'required|array',
        ]);

        $this->moderationService->restrictAccount(
            $validated['account_type'],
            $validated['account_id'],
            $validated['restrictions'],
            $moderator
        );

        return response()->json(['message' => 'Account restricted']);
    }
}
