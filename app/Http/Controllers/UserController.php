<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\AuthUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use App\Mail\PasswordResetMail;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        if ($request->validated()) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            event(new Registered($user));

            return response()->json([
                'message' => 'Account created successfully. Please verify your email.'
            ]);
        }
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $token = Str::random(40);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        Mail::raw("Your password reset token is: {$token}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset Token');
        });

        return response()->json([
            'message' => 'Password reset token sent.',
            'token' => $token, 
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return response()->json(['error' => 'Invalid or expired token'], 400);
        }

        if (now()->diffInMinutes($resetToken->created_at) > 40) {
            return response()->json(['error' => 'Token has expired'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset successfully']);
    }

    public function auth(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Please verify your email before logging in.'], 403);
            }

            $token = $user->createToken('YourApp')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'currentToken' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
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
