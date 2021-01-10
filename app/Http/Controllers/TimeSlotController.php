<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function index(Request $request)
    {
        \Log::info($request);
    }
}
