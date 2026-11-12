<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\HomepageSection;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HomepageController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $sections = HomepageSection::orderBy('sort_order', 'asc')->get();

        return response()->json(['data' => $sections]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'config' => 'array',
        ]);

        $section = HomepageSection::create($validated);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'create_homepage_section',
            'subject_type' => HomepageSection::class,
            'subject_id' => $section->id,
            'new_values' => $validated,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['data' => $section], 201);
    }

    public function update(Request $request, HomepageSection $section)
    {
        abort_unless($request->user()->hasPermission('homepage.manage'), 403);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:50',
            'sort_order' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
            'config' => 'sometimes|array',
        ]);

        $section->update($validated);

        return response()->json(['data' => $section]);
    }
}
