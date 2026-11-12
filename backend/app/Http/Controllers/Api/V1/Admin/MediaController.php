<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\MediaAsset;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('media.manage'), 403);

        $query = MediaAsset::query();

        if ($search = $request->get('search')) {
            $query->where('file_path', 'like', "%{$search}%");
        }

        $media = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $media->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $media->currentPage(),
                    'last_page' => $media->lastPage(),
                    'per_page' => $media->perPage(),
                    'total' => $media->total(),
                ],
            ],
        ]);
    }

    public function destroy(Request $request, MediaAsset $media)
    {
        abort_unless($request->user()->hasPermission('media.manage'), 403);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'remove_media',
            'subject_type' => MediaAsset::class,
            'subject_id' => $media->id,
            'old_values' => $media->getAttributes(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $media->delete();

        return response()->json(['message' => 'Media removed']);
    }
}
