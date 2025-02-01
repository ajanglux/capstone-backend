<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        // Find the user by the ID
        $user = User::findOrFail($id);

        // Validate the hash
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 400);
        }

        // Mark the user as verified
        $user->markEmailAsVerified();
        
        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully.']);
    }
}