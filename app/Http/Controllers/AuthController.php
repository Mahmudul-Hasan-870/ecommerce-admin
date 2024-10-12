<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // Validate request inputs
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
        ]);

        // Generate auth token
        $token = $user->createToken('auth')->plainTextToken;

        // Return success response
        return response()->json(['status' => 'success', 'message' => 'User registered successfully', 'token' => $token]);
    }

    // Login method
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid email or password'], 401);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json(['status' => 'success', 'message' => 'Login successful', 'token' => $token]);
    }

    // Update user
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $user = $request->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['status' => 'success', 'message' => 'User updated successfully', 'data' => $user]);
    }

    // Logout user
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Delete the user's tokens
            $user->tokens()->delete();

            // Delete the user
            $user->delete();

            return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
        }
    }

    // Get authenticated user details
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}
