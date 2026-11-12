<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentGatewayController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('payment-gateways.manage'), 403);

        $gateways = config('payment_gateways', [
            ['name' => 'M-Pesa', 'slug' => 'mpesa', 'enabled' => true, 'sandbox' => true],
            ['name' => 'Stripe', 'slug' => 'stripe', 'enabled' => true, 'sandbox' => true],
        ]);

        return response()->json(['data' => $gateways]);
    }

    public function update(Request $request, $gateway)
    {
        abort_unless($request->user()->hasPermission('payment-gateways.manage'), 403);

        $validated = $request->validate([
            'enabled' => 'boolean',
            'sandbox' => 'boolean',
            'config' => 'array',
        ]);

        return response()->json(['message' => 'Payment gateway updated', 'data' => $validated]);
    }
}
