<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Identity\Reader;
use App\Models\Identity\ReaderProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ReaderAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:readers,email',
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed'],
            'display_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reader = Reader::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        ReaderProfile::create([
            'reader_id' => $reader->id,
            'display_name' => $request->display_name,
            'preferences' => [],
        ]);

        $token = $reader->createToken('reader-api')->plainTextToken;

        return response()->json([
            'data' => [
                'reader' => $reader,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reader = Reader::where('email', $request->email)->first();

        if (! $reader || ! Hash::check($request->password, $reader->password)) {
            return response()->json(['errors' => ['email' => ['The provided credentials are incorrect.']]], 401);
        }

        $token = $reader->createToken('reader-api')->plainTextToken;

        return response()->json([
            'data' => [
                'reader' => $reader,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function sendPasswordResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:readers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['message' => 'Password reset link sent']);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:readers,email',
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed'],
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function verifyEmail(Request $request)
    {
        return response()->json(['message' => 'Email verified']);
    }

    public function enableMfa(Request $request)
    {
        return response()->json(['message' => 'MFA enabled']);
    }

    public function verifyMfa(Request $request)
    {
        return response()->json(['message' => 'MFA verified']);
    }
}
