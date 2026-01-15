<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuidelineController extends Controller
{
    /**
     * Display the inventory system guidelines and manual.
     */
    public function index()
    {
        // Currently returning a static view, but you can pass 
        // variables here if you decide to store guidelines in the DB.
        return view('staff.guidelines.index');
    }

    /**
     * Optional: Handle PDF Manual Downloads
     */
    public function downloadManual()
    {
        $filePath = public_path('downloads/College_Inventory_Manual_2025.pdf');
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'Manual file not found on server.');
    }
}