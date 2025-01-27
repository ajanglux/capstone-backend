<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\AuthUserRequest;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        if ($request->validated()) {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'message' => 'Account created successfully'
            ]);
        }
    }
    
    public function auth(AuthUserRequest $request)
    {
        if($request->validated()) {
            $user = User::where('email', $request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'These credentials do not match any of our records'
                ], 401);
            }

            $token = $user->createToken('new_user')->plainTextToken;
            return response()->json(
            [
                'user' => $user,
                'message' => 'Logged in Successfully',
                'currentToken' => $token
            ],
            200
        );
        
        }
    
        return response()->json(['error' => 'Invalid data provided'], 422);
    }

    public function fetchUserData()
    {
        $user = Auth::user();  // Get authenticated user

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'user' => $user,
            'message' => 'User Data Successfully Fetched',
        ], 200);
    }

    public function updateUserProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|digits:11',
            'address' => 'required|string|max:255',
        ]);

        $user->update($request->all());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ], 200);
    }

    

    public function logout (Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

}
