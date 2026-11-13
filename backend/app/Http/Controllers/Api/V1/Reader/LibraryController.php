<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\DigitalAccess;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        $access = DigitalAccess::where('reader_id', $reader->id)
            ->with('book.writer', 'book.productCategory')
            ->orderBy('created_at', 'desc')
            ->get();

        $items = $access->map(function (DigitalAccess $entry) {
            $book = $entry->book;

            return [
                'id' => $entry->id,
                'access_token' => $entry->access_token,
                'expires_at' => $entry->expires_at,
                'purchased_at' => $entry->created_at,
                'book' => $book,
            ];
        });

        return response()->json(['data' => $items]);
    }
}
