<?php

namespace App\Services\Media;

use App\Models\Identity\MediaAsset;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService extends \App\Services\Shared\BaseService
{
    public function uploadMedia(UploadedFile $file, string $ownerType, string $ownerId, string $uploaderType, string $uploaderId, array $options = []): MediaAsset
    {
        $disk = config('filesystems.default', 'local');
        $path = $file->storeAs(
            'uploads/' . date('Y/m/d'),
            (string) Str::uuid() . '.' . $file->getClientOriginalExtension(),
            $disk
        );

        $media = MediaAsset::create([
            'id' => (string) Str::uuid(),
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'uploader_type' => $uploaderType,
            'uploader_id' => $uploaderId,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'width' => $options['width'] ?? null,
            'height' => $options['height'] ?? null,
            'alt_text' => $options['alt_text'] ?? null,
            'is_reported' => false,
        ]);

        return $media;
    }

    public function deleteMedia(MediaAsset $media): void
    {
        $disk = config('filesystems.default', 'local');
        Storage::disk($disk)->delete($media->file_path);
        $media->delete();
    }

    public function getMediaByOwner(string $ownerType, string $ownerId): \Illuminate\Database\Eloquent\Collection
    {
        return MediaAsset::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllMedia(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = MediaAsset::query();

        if (isset($filters['search'])) {
            $query->where('file_path', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_reported'])) {
            $query->where('is_reported', $filters['is_reported']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}
