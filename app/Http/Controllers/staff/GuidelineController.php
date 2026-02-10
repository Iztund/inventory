<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, Log, Auth};
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuidelineController extends Controller
{
    /**
     * Display the inventory system guidelines and manual page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // You can later fetch dynamic content from DB or config
        $manualInfo = [
            'title'       => 'College of Medicine Inventory System Manual',
            'version'     => '2025.1',
            'last_updated'=> 'January 2025',
            'file_size'   => $this->getManualFileSize(),
            'file_exists' => $this->manualFileExists(),
        ];

        return view('staff.guidelines.index', compact('manualInfo'));
    }

    /**
     * Download the official PDF inventory manual.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadManual()
    {
        $filePath = 'manuals/College_Inventory_Manual_2025.pdf'; // Relative to storage/app/public

        if (!Storage::disk('public')->exists($filePath)) {
            Log::warning("Manual file not found: {$filePath}", ['user_id' => Auth::id() ?? 'guest']);

            return redirect()->back()
                ->with('error', 'The manual file is currently unavailable. Please contact IT support.');
        }

        $fullPath = Storage::disk('public')->path($filePath);
        $fileName = basename($filePath);

        return response()->download($fullPath, $fileName, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Check if the manual file exists in storage.
     *
     * @return bool
     */
    private function manualFileExists(): bool
    {
        $filePath = 'manuals/College_Inventory_Manual_2025.pdf';
        return Storage::disk('public')->exists($filePath);
    }

    /**
     * Get human-readable file size of the manual (if exists).
     *
     * @return string
     */
    private function getManualFileSize(): string
    {
        $filePath = 'manuals/College_Inventory_Manual_2025.pdf';

        if (!Storage::disk('public')->exists($filePath)) {
            return 'N/A';
        }

        $bytes = Storage::disk('public')->size($filePath);

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' bytes';
    }
}