<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\Reader;
use App\Services\Reader\ReaderService;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    protected ReaderService $readerService;

    public function __construct(ReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    public function show(Request $request)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $preferences = $this->readerService->getPreferences($reader);
        $allCategories = $this->readerService->getAllCategories();

        return response()->json([
            'data' => [
                'selected' => $preferences,
                'all' => $allCategories,
            ],
        ]);
    }

    public function update(Request $request, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $validated = $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'uuid|exists:categories,id',
        ]);

        $readerService->updatePreferences($reader, $validated['category_ids']);

        return response()->json(['message' => 'Preferences updated']);
    }
}
