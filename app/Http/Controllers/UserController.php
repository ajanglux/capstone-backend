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
                'age' => $request->age, 
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

        $token = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        Mail::raw("\nYour password reset code is: {$token}", function ($message) use ($user) {
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
            'password' => 'required|min:8|confirmed',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return response()->json(['error' => 'Invalid or expired token'], 400);
        }

        if (now()->diffInMinutes($resetToken->created_at) > 40) {
            return response()->json(['error' => 'Token has expired'], 400);
        }

        $user = User::where('email', $resetToken->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return response()->json(['message' => 'Password has been reset successfully']);
    }


    public function auth(Request $request)
    {
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['error' => 'No account found with this email.'], 404);
        }
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Incorrect password. Please try again.'], 401);
        }
    
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
            'age' => 'required|integer|min:1|max:120',
        ]);
    
        $user->update($request->all());
    
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ], 200);
    }
    
    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Check if the email is actually changing
        if ($request->email === $user->email) {
            return response()->json([
                'message' => 'The new email must be different from the current email.'
            ], 400);
        }

        // Update email and mark as unverified
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();

        // Trigger email verification
        event(new Registered($user));

        return response()->json([
            'message' => 'Email updated successfully. Please verify your new email.'
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
