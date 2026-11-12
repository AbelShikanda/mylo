<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Writer;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;

class WriterController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('writers.view'), 403);

        $query = Writer::with('profile');

        if ($search = $request->get('search')) {
            $query->where('email', 'like', "%{$search}%");
        }

        $writers = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $writers->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $writers->currentPage(),
                    'last_page' => $writers->lastPage(),
                    'per_page' => $writers->perPage(),
                    'total' => $writers->total(),
                ],
            ],
        ]);
    }

    public function suspend(Request $request, string $writerId)
    {
        abort_unless($request->user()->hasPermission('writers.suspend'), 403);

        $writer = Writer::withTrashed()->findOrFail($writerId);

        $writer->delete();

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'suspend_writer',
            'subject_type' => Writer::class,
            'subject_id' => $writer->id,
            'new_values' => ['status' => 'suspended'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Writer suspended']);
    }

    public function reinstate(Request $request, string $writerId)
    {
        abort_unless($request->user()->hasPermission('writers.reinstate'), 403);

        $writer = Writer::withTrashed()->findOrFail($writerId);

        $writer->restore();

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'reinstate_writer',
            'subject_type' => Writer::class,
            'subject_id' => $writer->id,
            'new_values' => ['status' => 'active'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Writer reinstated']);
    }
}
