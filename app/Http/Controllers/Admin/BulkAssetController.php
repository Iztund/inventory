<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth, Storage, Validator};
use App\Models\{Asset, BulkImport, Category, Subcategory, Faculty, Department, Office, Unit, Institute};
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BulkAssetController extends Controller
{
    /**
     * Show the bulk import interface
     */
    public function index()
    {
        $recentImports = BulkImport::with(['importedBy.profile'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_imports' => BulkImport::count(),
            'total_assets_imported' => BulkImport::sum('successful_imports'),
            'pending_imports' => BulkImport::where('status', 'pending')->count(),
            'failed_imports' => BulkImport::where('status', 'failed')->count(),
        ];

        return view('admin.BulkImports.index', compact('recentImports', 'stats'));
    }

    /**
     * Show the manual entry form
     */
    public function createManual()
    {
        $dropdowns = $this->getDropdownData();
        return view('admin.BulkImports.manual_entry', $dropdowns);
    }

    /**
     * Store manually entered asset
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|in:faculty,department,office,unit,institute',
            'entity_id' => 'required|integer',
            'category_id' => 'required|exists:categories,category_id',
            'subcategory_id' => 'nullable|exists:subcategories,subcategory_id',
            'item_name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'asset_tag' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:available,assigned,maintenance,retired',
            'condition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $bulkImport = BulkImport::create([
                    'imported_by_user_id' => Auth::id(),
                    'import_type' => 'manual',
                    'entity_type' => $validated['entity_type'],
                    'entity_id' => $validated['entity_id'],
                    'total_rows' => 1,
                    'successful_imports' => 1,
                    'failed_imports' => 0,
                    'status' => 'completed',
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);

                $entityColumns = $this->getEntityColumnMapping($validated['entity_type'], $validated['entity_id']);

                $asset = Asset::create(array_merge($validated, $entityColumns, [
                    'bulk_import_id' => $bulkImport->import_id,
                    'serial_number' => $validated['serial_number'] ?? 'AUTO-' . strtoupper(uniqid()),
                    'asset_tag' => null,
                ]));

                $this->generateAssetTag($asset, $validated['entity_type']);
            });

            return redirect()->route('admin.bulk-assets.index')
                ->with('success', 'Asset added successfully with auto-generated tag.');

        } catch (\Exception $e) {
            Log::error('Manual asset creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create asset: ' . $e->getMessage());
        }
    }

    /**
     * Show CSV upload form
     */
    public function createCsv()
    {
        $dropdowns = $this->getDropdownData();
        return view('admin.BulkImports.csv_uploads', $dropdowns);
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="asset_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'item_name',
                'category_name',
                'subcategory_name',
                'serial_number',
                'quantity',
                'purchase_price',
                'purchase_date',
                'status',
                'condition',
                'notes'
            ]);

            fputcsv($file, [
                'Dell Latitude 5420 Laptop',
                'ICT & Electronics',
                'Computers',
                'SN123456789',
                '1',
                '450000.00',
                '2024-01-15',
                'available',
                'new',
                'Procured via TETFund grant'
            ]);

            fputcsv($file, [
                'HP LaserJet Pro M404dn',
                'ICT & Electronics',
                'Printers',
                'HP-SN987654',
                '2',
                '125000.00',
                '2024-02-20',
                'available',
                'good',
                'Office equipment'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process CSV/Excel upload
     */
    public function processCsv(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls,txt|max:10240',
            'entity_type' => 'required|in:faculty,department,office,unit,institute',
            'entity_id' => 'required|integer',
        ]);

        try {
            $file = $request->file('import_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $entityType = $request->entity_type;
            $entityId = $request->entity_id;

            // Create bulk import record
            $bulkImport = BulkImport::create([
                'imported_by_user_id' => Auth::id(),
                'import_type' => in_array($extension, ['xlsx', 'xls']) ? 'excel' : 'csv',
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'original_filename' => $file->getClientOriginalName(),
                'status' => 'processing',
                'started_at' => now(),
            ]);

            // Process based on file type
            if (in_array($extension, ['xlsx', 'xls'])) {
                $result = $this->processExcelFile($file, $entityType, $entityId, $bulkImport);
            } else {
                $result = $this->processCSVFile($file, $entityType, $entityId, $bulkImport);
            }

            // Update bulk import with results
            $bulkImport->update([
                'total_rows' => $result['total'],
                'successful_imports' => $result['success'],
                'failed_imports' => $result['failed'],
                'error_log' => $result['errors'],
                'status' => $result['success'] > 0 ? 'completed' : 'failed',
                'completed_at' => now(),
            ]);

            // Generate asset tags
            $this->generateAssetTagsForImport($bulkImport, $entityType);

            if ($result['success'] > 0) {
                return redirect()->route('admin.bulk-assets.show', $bulkImport->import_id)
                    ->with('success', "Import completed: {$result['success']} successful, {$result['failed']} failed.");
            } else {
                return redirect()->route('admin.bulk-assets.show', $bulkImport->import_id)
                    ->with('error', "Import failed: All {$result['total']} rows had errors. Check error log below.");
            }

        } catch (\Exception $e) {
            Log::error('File import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Process Excel file (.xlsx, .xls)
     */
    private function processExcelFile($file, $entityType, $entityId, $bulkImport)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // First row is header
            $header = array_shift($rows);
            
            // Clean headers
            $header = array_map(function($h) {
                return strtolower(trim($h));
            }, $header);

            $total = count($rows);
            $success = 0;
            $failed = 0;
            $errors = [];
            $entityColumns = $this->getEntityColumnMapping($entityType, $entityId);

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    $total--;
                    continue;
                }

                try {
                    $data = array_combine($header, $row);
                    $this->importSingleRow($data, $entityColumns, $bulkImport->import_id);
                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                        'data' => implode(', ', array_slice($row, 0, 3)) . '...'
                    ];
                    Log::error("Excel row {$rowNumber} failed: " . $e->getMessage());
                }
            }

            return [
                'total' => $total,
                'success' => $success,
                'failed' => $failed,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            Log::error('Excel processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process CSV file
     */
    private function processCSVFile($file, $entityType, $entityId, $bulkImport)
    {
        try {
            // Read file with proper encoding handling
            $content = file_get_contents($file->getRealPath());
            
            // Remove BOM if present
            $content = str_replace("\xEF\xBB\xBF", '', $content);
            
            // Parse CSV
            $lines = str_getcsv($content, "\n");
            $csvData = array_map('str_getcsv', $lines);
            
            // Remove completely empty rows
            $csvData = array_filter($csvData, function($row) {
                return !empty(array_filter($row));
            });
            
            $csvData = array_values($csvData); // Re-index
            
            if (empty($csvData)) {
                throw new \Exception('CSV file is empty or could not be read.');
            }

            $header = array_shift($csvData);
            
            // Clean headers: remove BOM, trim, lowercase
            $header = array_map(function($h) {
                $h = trim($h);
                $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h); // Remove non-printable
                return strtolower($h);
            }, $header);

            Log::info('CSV Headers detected: ' . implode(', ', $header));

            $total = count($csvData);
            $success = 0;
            $failed = 0;
            $errors = [];
            $entityColumns = $this->getEntityColumnMapping($entityType, $entityId);

            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2;

                try {
                    // Check column count
                    if (count($header) !== count($row)) {
                        throw new \Exception("Column mismatch: Expected " . count($header) . " columns, got " . count($row));
                    }

                    $data = array_combine($header, $row);
                    $this->importSingleRow($data, $entityColumns, $bulkImport->import_id);
                    $success++;

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                        'data' => implode(', ', array_slice($row, 0, 3)) . '...'
                    ];
                    Log::error("CSV row {$rowNumber} failed: " . $e->getMessage());
                }
            }

            return [
                'total' => $total,
                'success' => $success,
                'failed' => $failed,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            Log::error('CSV processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Import a single row (used by both CSV and Excel)
     */
    private function importSingleRow($data, $entityColumns, $bulkImportId)
    {
        // Find category - handle case-insensitive matching
        $categoryName = trim($data['category_name'] ?? '');
        
        if (empty($categoryName)) {
            throw new \Exception("Category name is missing");
        }

        $category = Category::whereRaw('LOWER(category_name) = ?', [strtolower($categoryName)])->first();

        if (!$category) {
            throw new \Exception("Category '{$categoryName}' not found. Available categories: " . 
                Category::pluck('category_name')->implode(', '));
        }

        // Find subcategory if provided
        $subcategory = null;
        $subcatName = trim($data['subcategory_name'] ?? '');
        
        if (!empty($subcatName)) {
            $subcategory = Subcategory::whereRaw('LOWER(subcategory_name) = ?', [strtolower($subcatName)])
                ->where('category_id', $category->category_id)
                ->first();
                
            if (!$subcategory) {
                Log::warning("Subcategory '{$subcatName}' not found for category '{$categoryName}'");
            }
        }

        // Prepare asset data
        $assetData = [
            'item_name' => trim($data['item_name'] ?? ''),
            'category_id' => $category->category_id,
            'subcategory_id' => $subcategory?->subcategory_id,
            'serial_number' => !empty($data['serial_number']) ? trim($data['serial_number']) : 'AUTO-' . strtoupper(uniqid()),
            'asset_tag' => null, // Generated after import
            'quantity' => isset($data['quantity']) && is_numeric($data['quantity']) ? (int)$data['quantity'] : 1,
            'purchase_price' => isset($data['purchase_price']) && is_numeric($data['purchase_price']) ? (float)$data['purchase_price'] : 0,
            'purchase_date' => !empty($data['purchase_date']) ? $this->parseDate($data['purchase_date']) : null,
            'status' => !empty($data['status']) ? strtolower(trim($data['status'])) : 'available',
            'condition' => !empty($data['condition']) ? trim($data['condition']) : null,
            'notes' => $data['notes'] ?? null,
            'bulk_import_id' => $bulkImportId,
        ];

        $assetData = array_merge($assetData, $entityColumns);

        // Validation
        $validator = Validator::make($assetData, [
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'status' => 'required|in:available,assigned,maintenance,retired',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        // Create asset
        Asset::create($assetData);
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        try {
            // Try common formats
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd-m-Y', 'm-d-Y'];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try Carbon parse as fallback
            return Carbon::parse($dateString)->format('Y-m-d');
            
        } catch (\Exception $e) {
            Log::warning("Could not parse date '{$dateString}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Show import details
     */
    public function show($import_id)
    {
        $import = BulkImport::with(['importedBy.profile', 'assets.category', 'assets.subcategory'])
            ->findOrFail($import_id);

        $entity = null;
        $entityModel = null;

        switch ($import->entity_type) {
            case 'faculty':    $entityModel = Faculty::class; break;
            case 'department': $entityModel = Department::class; break;
            case 'office':     $entityModel = Office::class; break;
            case 'unit':       $entityModel = Unit::class; break;
            case 'institute':  $entityModel = Institute::class; break;
        }

        if ($entityModel) {
            $primaryKey = ($import->entity_type === 'department' ? 'dept' : $import->entity_type) . '_id';
            $entity = $entityModel::where($primaryKey, $import->entity_id)->first();
        }

        return view('admin.BulkImports.show', compact('import', 'entity'));
    }

    /**
     * Generate asset tags for all assets in an import batch
     */
    public function generateTags($import_id)
    {
        try {
            $import = BulkImport::findOrFail($import_id);
            
            $generatedCount = $this->generateAssetTagsForImport($import, $import->entity_type);

            return redirect()->back()
                ->with('success', "Generated {$generatedCount} asset tags successfully.");

        } catch (\Exception $e) {
            Log::error('Asset tag generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate asset tags.');
        }
    }

    /**
     * Delete an import and its assets
     */
    public function destroy($import_id)
    {
        try {
            $import = BulkImport::findOrFail($import_id);
            
            DB::transaction(function () use ($import) {
                $import->assets()->delete();
                $import->delete();
            });

            return redirect()->route('admin.bulk-assets.index')
                ->with('success', 'Import and associated assets deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Import deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete import.');
        }
    }

    /**
     * Generate asset tag for a single asset
     */
    private function generateAssetTag($asset, $entityType)
    {
        try {
            $prefix = match($entityType) {
                'unit'       => $asset->unit?->unit_code ?? 'UNIT',
                'department' => $asset->department?->dept_code ?? 'DEPT',
                'institute'  => $asset->institute?->institute_code ?? 'INST',
                'office'     => $asset->office?->office_code ?? 'OFFICE',
                'faculty'    => $asset->faculty?->faculty_code ?? 'FAC',
                default      => 'COM',
            };
            
            $year = $asset->created_at ? $asset->created_at->format('y') : date('y');
            $catCode = $asset->category?->category_code ?? 'XX';
            $subcatCode = $asset->subcategory?->subcategory_code ?? 'XX';
            $serial = str_pad($asset->asset_id, 6, '0', STR_PAD_LEFT);

            $assetTag = "COM/{$prefix}/{$catCode}/{$subcatCode}/{$year}/{$serial}";
            $asset->update(['asset_tag' => $assetTag]);
            
            Log::info("Generated tag: {$assetTag} for asset {$asset->asset_id}");
            return $assetTag;

        } catch (\Exception $e) {
            Log::error("Tag generation failed for asset {$asset->asset_id}: " . $e->getMessage());
            $asset->update(['asset_tag' => 'PENDING_TAG_' . $asset->asset_id]);
            return null;
        }
    }

    /**
     * Generate asset tags for all assets in an import batch
     */
    private function generateAssetTagsForImport($bulkImport, $entityType)
    {
        $assets = $bulkImport->assets()->whereNull('asset_tag')->get();
        $generatedCount = 0;

        foreach ($assets as $asset) {
            $this->generateAssetTag($asset, $entityType);
            $generatedCount++;
        }

        Log::info("Generated {$generatedCount} asset tags for import {$bulkImport->import_id}");
        return $generatedCount;
    }

    /**
     * Get entity column mapping
     */
    private function getEntityColumnMapping($entityType, $entityId)
    {
        return match($entityType) {
            'faculty' => ['current_faculty_id' => $entityId],
            'department' => ['current_dept_id' => $entityId],
            'office' => ['current_office_id' => $entityId],
            'unit' => ['current_unit_id' => $entityId],
            'institute' => ['current_institute_id' => $entityId],
            default => [],
        };
    }

    /**
     * Get dropdown data for forms
     */
    private function getDropdownData()
    {
        return [
            'categories' => Category::orderBy('category_name')->get(),
            'subcategories' => Subcategory::orderBy('subcategory_name')->get(),
            'faculties' => Faculty::where('is_active', 'active')->orderBy('faculty_name')->get(),
            'departments' => Department::where('is_active', 'active')->orderBy('dept_name')->get(),
            'offices' => Office::where('is_active', 'active')->orderBy('office_name')->get(),
            'units' => Unit::where('is_active', 'active')->orderBy('unit_name')->get(),
            'institutes' => Institute::where('is_active', 'active')->orderBy('institute_name')->get(),
        ];
    }
}