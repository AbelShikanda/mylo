<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Reader;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('readers.view'), 403);

        $query = Reader::with('profile');

        if ($search = $request->get('search')) {
            $query->where('email', 'like', "%{$search}%");
        }

        $readers = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $readers->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $readers->currentPage(),
                    'last_page' => $readers->lastPage(),
                    'per_page' => $readers->perPage(),
                    'total' => $readers->total(),
                ],
            ],
        ]);
    }

    public function show(Request $request, Reader $reader)
    {
        abort_unless($request->user()->hasPermission('readers.view'), 403);
        return response()->json(['data' => $reader->load('profile')]);
    }

    public function suspend(Request $request, string $readerId)
    {
        abort_unless($request->user()->hasPermission('readers.suspend'), 403);

        $reader = Reader::withTrashed()->findOrFail($readerId);

        $reader->delete();

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'suspend_reader',
            'subject_type' => Reader::class,
            'subject_id' => $reader->id,
            'new_values' => ['status' => 'suspended'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Reader suspended']);
    }

    public function reinstate(Request $request, string $readerId)
    {
        abort_unless($request->user()->hasPermission('readers.reinstate'), 403);

        $reader = Reader::withTrashed()->findOrFail($readerId);

        $reader->restore();

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'reinstate_reader',
            'subject_type' => Reader::class,
            'subject_id' => $reader->id,
            'new_values' => ['status' => 'active'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Reader reinstated']);
    }
}
