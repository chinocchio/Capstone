<?php

namespace App\Http\Controllers;

use App\Models\Mac;
use Illuminate\Http\Request;

class PlotController extends Controller
{
    public function show()
    {
        // Fetch all MACs or specific data related to the MAC laboratory
        $macs = Mac::all(); // Assuming 'Mac' is your model for the macs table

        // Return the MACs data to your view
        return view('users.plotting', compact('macs')); // Adjust the view path as necessary
    }
}
