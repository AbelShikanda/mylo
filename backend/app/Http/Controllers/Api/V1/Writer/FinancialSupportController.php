<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;

class FinancialSupportController extends Controller
{
    public function activate() { return response()->json(['message' => 'activated']); }
    public function deactivate() { return response()->json(['message' => 'deactivated']); }
}
