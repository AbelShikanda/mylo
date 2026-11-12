<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\Reader;
use App\Services\Reader\ReaderService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReaderService $readerService;

    public function __construct(ReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    public function store(Request $request, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $validated = $request->validate([
            'reportable_type' => 'required|string|in:article,comment,book,media',
            'reportable_id' => 'required|uuid',
            'reason' => 'required|string|max:1000',
        ]);

        $readerService->reportContent(
            $validated['reportable_type'],
            $validated['reportable_id'],
            'reader',
            $reader->id,
            $validated['reason']
        );

        return response()->json(['message' => 'Report submitted'], 201);
    }
}
