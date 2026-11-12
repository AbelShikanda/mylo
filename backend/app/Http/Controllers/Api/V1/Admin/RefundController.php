<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Refund;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.refunds.manage'), 403);

        $refunds = Refund::with('transaction')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $refunds->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $refunds->currentPage(),
                    'last_page' => $refunds->lastPage(),
                    'per_page' => $refunds->perPage(),
                    'total' => $refunds->total(),
                ],
            ],
        ]);
    }

    public function approve(Request $request, Refund $refund)
    {
        abort_unless($request->user()->hasPermission('finance.refunds.manage'), 403);

        $refund->update(['status' => 'approved', 'processed_at' => now()]);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'approve_refund',
            'subject_type' => Refund::class,
            'subject_id' => $refund->id,
            'new_values' => ['status' => 'approved'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Refund approved']);
    }

    public function reject(Request $request, Refund $refund)
    {
        abort_unless($request->user()->hasPermission('finance.refunds.manage'), 403);

        $refund->update(['status' => 'rejected']);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'reject_refund',
            'subject_type' => Refund::class,
            'subject_id' => $refund->id,
            'new_values' => ['status' => 'rejected'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Refund rejected']);
    }
}
