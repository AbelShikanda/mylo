<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\MediaAsset;
use App\Models\Identity\Writer;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class MediaController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    protected function checkWriter(Request $request): Writer
    {
        $user = $request->user('writer');
        if (! $user instanceof Writer) {
            abort(404);
        }
        return $user;
    }

    public function index(Request $request)
    {
        $writer = $this->checkWriter($request);

        $media = $this->mediaService->getMediaByOwner('writer', $writer->id);

        return response()->json(['data' => $media]);
    }

    public function store(Request $request, MediaService $mediaService)
    {
        $writer = $this->checkWriter($request);

        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $media = $mediaService->uploadMedia(
            $validated['file'],
            'writer',
            $writer->id,
            'writer',
            $writer->id,
            ['alt_text' => $validated['alt_text'] ?? null]
        );

        return response()->json(['data' => $media], 201);
    }

    public function destroy(Request $request, MediaAsset $media, MediaService $mediaService)
    {
        $writer = $this->checkWriter($request);
        abort_unless($media->owner_type === 'writer' && $media->owner_id === $writer->id, 403);

        $mediaService->deleteMedia($media);

        return response()->json(['message' => 'Media deleted']);
    }
}
