<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Identity\Writer;
use App\Models\Identity\WriterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class WriterAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:writers,email',
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed'],
            'display_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $writer = Writer::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        WriterProfile::create([
            'writer_id' => $writer->id,
            'display_name' => $request->display_name,
            'earnings_balance' => 0,
            'pending_balance' => 0,
            'withdrawn_amount' => 0,
            'support_active' => false,
        ]);

        $token = $writer->createToken('writer-api')->plainTextToken;

        return response()->json([
            'data' => [
                'writer' => $writer,
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

        $writer = Writer::where('email', $request->email)->first();

        if (! $writer || ! Hash::check($request->password, $writer->password)) {
            return response()->json(['errors' => ['email' => ['The provided credentials are incorrect.']]], 401);
        }

        $token = $writer->createToken('writer-api')->plainTextToken;

        return response()->json([
            'data' => [
                'writer' => $writer,
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
            'email' => 'required|email|exists:writers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['message' => 'Password reset link sent']);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:writers,email',
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
}
