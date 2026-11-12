<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\PlatformConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConfigController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('config.manage'), 403);

        $configs = PlatformConfig::all()->keyBy('key');

        return response()->json(['data' => $configs]);
    }

    public function update(Request $request, $key)
    {
        abort_unless($request->user()->hasPermission('config.manage'), 403);

        $validated = $request->validate([
            'value' => 'required|string',
        ]);

        $config = PlatformConfig::updateOrCreate(
            ['key' => $key],
            ['value' => $validated['value'], 'type' => 'string']
        );

        return response()->json(['data' => $config]);
    }
}
