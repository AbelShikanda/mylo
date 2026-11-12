<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('audit-logs.view'), 403);

        $logs = AuditLog::with('administrator')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $logs->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ],
            ],
        ]);
    }
}
