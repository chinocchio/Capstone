<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PinVerificationController extends Controller
{
    /**
     * Verify the provided PIN.
     */
    public function verifyPin(Request $request)
    {
        // Validate that the PIN is provided and is exactly 4 digits
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        // Retrieve all users and check PIN
        $users = User::all();

        foreach ($users as $user) {
            if (Hash::check($request->pin, $user->password)) {
                // Return the user data if authentication is successful
                return response()->json([
                    'success' => true,
                    'user' => $user,
                ]);
            }
        }

        // If no user found with the provided PIN, return an error response
        return response()->json(['success' => false, 'message' => 'Invalid PIN'], 401);
    }

    public function verifyPinForDoor(Request $request)
    {
        // Validate that the PIN is provided and is exactly 4 digits
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        // Retrieve the user with the matching PIN
        $user = User::where('pin', $request->pin)->first();

        if ($user) {
            // Return the user data if the PIN matches
            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        }

        // If no user found with the provided PIN, return an error response
        return response()->json(['success' => false, 'message' => 'Invalid PIN'], 401);
    }
}
