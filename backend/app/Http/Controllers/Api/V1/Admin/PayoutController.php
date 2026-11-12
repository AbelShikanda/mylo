<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\PayoutRequest;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.payouts.manage'), 403);

        $payouts = PayoutRequest::with('writer')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $payouts->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $payouts->currentPage(),
                    'last_page' => $payouts->lastPage(),
                    'per_page' => $payouts->perPage(),
                    'total' => $payouts->total(),
                ],
            ],
        ]);
    }

    public function approve(Request $request, PayoutRequest $payout)
    {
        abort_unless($request->user()->hasPermission('finance.payouts.manage'), 403);

        $payout->update(['status' => 'approved', 'processed_at' => now()]);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'approve_payout',
            'subject_type' => PayoutRequest::class,
            'subject_id' => $payout->id,
            'new_values' => ['status' => 'approved'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Payout approved']);
    }

    public function reject(Request $request, PayoutRequest $payout)
    {
        abort_unless($request->user()->hasPermission('finance.payouts.manage'), 403);

        $payout->update(['status' => 'rejected']);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'reject_payout',
            'subject_type' => PayoutRequest::class,
            'subject_id' => $payout->id,
            'new_values' => ['status' => 'rejected'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Payout rejected']);
    }
}
