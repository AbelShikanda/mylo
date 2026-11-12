<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Category;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('categories.manage'), 403);

        $categories = Category::orderBy('sort_order', 'asc')->get();

        return response()->json(['data' => $categories]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('categories.manage'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'parent_id' => 'nullable|uuid|exists:categories,id',
        ]);

        $category = Category::create($validated);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'create_category',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'new_values' => $validated,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['data' => $category], 201);
    }

    public function update(Request $request, Category $category)
    {
        abort_unless($request->user()->hasPermission('categories.manage'), 403);

        $oldValues = $category->getAttributes();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_featured' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
            'parent_id' => 'nullable|uuid|exists:categories,id',
        ]);

        $category->update($validated);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'update_category',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'old_values' => $oldValues,
            'new_values' => $validated,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['data' => $category]);
    }

    public function destroy(Request $request, Category $category)
    {
        abort_unless($request->user()->hasPermission('categories.manage'), 403);

        if ($category->articles()->exists()) {
            return response()->json([
                'errors' => ['category' => ['Cannot delete category with associated articles.']],
            ], 422);
        }

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'delete_category',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'old_values' => $category->getAttributes(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
