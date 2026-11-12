<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function checkout() { return response()->json(['message' => 'checkout']); }
}
