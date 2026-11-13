<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\EditorialSelection;
use App\Services\Administration\EditorialSchedulingService;
use Illuminate\Http\Request;

class EditorialSchedulingController extends Controller
{
    protected EditorialSchedulingService $schedulingService;

    public function __construct(EditorialSchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    public function getEditorialSelections(Request $request)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $filters = $request->only(['section', 'active_only']);
        $selections = $this->schedulingService->getEditorialSelections($filters);

        return response()->json([
            'data' => $selections->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $selections->currentPage(),
                    'last_page' => $selections->lastPage(),
                    'per_page' => $selections->perPage(),
                    'total' => $selections->total(),
                ],
            ],
        ]);
    }

    public function createEditorialSelection(Request $request)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $validated = $request->validate([
            'article_id' => 'required|uuid|exists:articles,id',
            'section' => 'required|string|max:100',
            'sort_order' => 'sometimes|integer|min:0',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date|after:starts_at',
        ]);

        $selection = $this->schedulingService->createEditorialSelection($validated);

        return response()->json(['data' => $selection], 201);
    }

    public function updateEditorialSelection(Request $request, EditorialSelection $selection)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $validated = $request->validate([
            'article_id' => 'sometimes|uuid|exists:articles,id',
            'section' => 'sometimes|string|max:100',
            'sort_order' => 'sometimes|integer|min:0',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date|after:starts_at',
        ]);

        $selection = $this->schedulingService->updateEditorialSelection($selection, $validated);

        return response()->json(['data' => $selection]);
    }

    public function deleteEditorialSelection(EditorialSelection $selection)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $this->schedulingService->deleteEditorialSelection($selection);

        return response()->json(['message' => 'Editorial selection deleted']);
    }

    public function getScheduledArticles(Request $request)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $filters = $request->only(['from_date', 'to_date']);
        $articles = $this->schedulingService->getScheduledArticles($filters);

        return response()->json([
            'data' => $articles->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                ],
            ],
        ]);
    }

    public function getHomepageSections(Request $request)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $sections = $this->schedulingService->getHomepageSections();

        return response()->json(['data' => $sections]);
    }
}
