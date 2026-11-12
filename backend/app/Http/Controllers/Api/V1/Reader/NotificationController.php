<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\Notification;
use App\Models\Identity\Reader;
use App\Services\Reader\ReaderService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected ReaderService $readerService;

    public function __construct(ReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    public function index(Request $request, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $notifications = $readerService->getNotifications($reader);

        return response()->json(['data' => $notifications]);
    }

    public function markAsRead(Request $request, Notification $notification, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        abort_unless($notification->recipient_type === 'reader' && $notification->recipient_id === $reader->id, 403);

        $readerService->markNotificationAsRead($reader, $notification->id);

        return response()->json(['message' => 'Notification marked as read']);
    }
}
