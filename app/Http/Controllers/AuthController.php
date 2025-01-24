<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Validator;

class AuthController extends Controller
{
    /**
     * Login a user and return a token.
     */
    public function login(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation Error', 'message' => $validator->errors()], 400);
        }

        // Attempt to authenticate the user using username and password
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();

            // Create a personal access token for the user using Sanctum
            $token = $user->createToken('YourAppName')->plainTextToken;
            $personalAccesToken = $user->tokens()->latest()->first();
            $personalAccesToken->expires_at = Carbon::now()->addMinutes(60);

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized', 'message' => 'Invalid credentials'], 401);
    }
}
