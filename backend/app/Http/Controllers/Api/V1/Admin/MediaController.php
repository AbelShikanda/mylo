<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\MediaAsset;
use App\Models\Identity\AuditLog;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('media.manage'), 403);

        $filters = $request->only(['search', 'is_reported']);
        $media = $this->mediaService->getAllMedia($filters);

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

    public function destroy(Request $request, MediaAsset $media, MediaService $mediaService)
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

        $mediaService->deleteMedia($media);

        return response()->json(['message' => 'Media removed']);
    }
}
