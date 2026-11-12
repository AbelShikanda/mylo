<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('roles.manage'), 403);

        $roles = AdminRole::with('permissions')->orderBy('name', 'asc')->get();

        return response()->json(['data' => $roles]);
    }
}
