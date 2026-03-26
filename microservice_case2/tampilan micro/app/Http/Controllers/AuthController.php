<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:patient,doctor,pharmacist',
            'date_of_birth' => 'required_if:role,patient|date',
            'gender' => 'required_if:role,patient|in:male,female,other',
            'phone_number' => 'required|string|max:20',
            'license_number' => 'required_if:role,doctor|unique:users',
            'specialization' => 'required_if:role,doctor',
            'hospital_name' => 'required_if:role,doctor',
            'pharmacy_name' => 'required_if:role,pharmacist',
            'pharmacy_license' => 'required_if:role,pharmacist|unique:users',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'hospital_name' => $validated['hospital_name'] ?? null,
            'pharmacy_name' => $validated['pharmacy_name'] ?? null,
            'pharmacy_license' => $validated['pharmacy_license'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'string|max:255',
            'phone_number' => 'string|max:20',
            'address' => 'string|max:500',
            'profile_picture' => 'image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Web Login
     */
    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('dashboard');
    }
}