<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\AuthUserRequest;

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
    

    public function logout (Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

}
