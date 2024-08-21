<?php

namespace App\Http\Controllers;

use App\Models\Fingerprint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FingerprintController extends Controller
{
 /**
     * Display a listing of the fingerprints.
     */
    public function index(): JsonResponse
    {
        $fingerprints = Fingerprint::all()->map(function ($fingerprint) {
            // Encode binary data to Base64
            $fingerprint->finger_print = base64_encode($fingerprint->finger_print);
            return $fingerprint;
        });

        return response()->json($fingerprints);
    }

    /**
     * Store a newly created fingerprint in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'fname' => 'required|string|max:255',
            'pin' => 'required|integer',
            'finger_print' => 'required|string', // Accept Base64 encoded string
        ]);

        // Decode Base64 data to binary
        $validatedData['finger_print'] = base64_decode($validatedData['finger_print']);

        $fingerprint = Fingerprint::create($validatedData);

        // Encode the binary data to Base64 for the response
        $fingerprint->finger_print = base64_encode($fingerprint->finger_print);

        return response()->json($fingerprint, 201);
    }
}
