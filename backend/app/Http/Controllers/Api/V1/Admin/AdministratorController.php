<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdministratorController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('administrators.manage'), 403);

        $admins = Administrator::with('roles')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $admins->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $admins->currentPage(),
                    'last_page' => $admins->lastPage(),
                    'per_page' => $admins->perPage(),
                    'total' => $admins->total(),
                ],
            ],
        ]);
    }
}
