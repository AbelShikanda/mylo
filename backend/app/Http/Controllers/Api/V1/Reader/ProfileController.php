<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\Reader;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected function checkReader(Request $request): Reader
    {
        $user = $request->user('reader');
        if (! $user instanceof Reader) {
            abort(404);
        }
        return $user;
    }

    public function show(Request $request)
    {
        $reader = $this->checkReader($request);
        return response()->json(['data' => $reader->load('profile')]);
    }

    public function update(Request $request)
    {
        $reader = $this->checkReader($request);
        return response()->json(['message' => 'updated']);
    }
}
