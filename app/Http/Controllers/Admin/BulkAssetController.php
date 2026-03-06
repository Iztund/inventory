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
    // 1. Validation (Matches your manual requirements)
    $validated = $request->validate([
        'entity_type' => 'required|in:faculty,department,office,unit,institute',
        'entity_id' => 'required|integer',
        'category_id' => 'required|exists:categories,category_id',
        'subcategory_id' => 'nullable|exists:subcategories,subcategory_id',
        'item_name' => 'required|string|max:255',
        'serial_number' => 'nullable|string|max:255',
        'quantity' => 'required|integer|min:1',
        'purchase_price' => 'required|numeric|min:0',
        'purchase_date' => 'nullable|date',
        'status' => 'required|in:available,assigned,maintenance,retired',
        'condition' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    try {
        $entityType = $validated['entity_type'];
        $entityId = $validated['entity_id'];

        // 2. Create bulk import record (Mirroring processCsv status: processing)
        $bulkImport = BulkImport::create([
            'imported_by_user_id' => Auth::id(),
            'import_type' => 'manual',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'original_filename' => 'Manual Entry',
            'status' => 'processing',
            'started_at' => now(),
        ]);

        Log::info("Created manual bulk import ID: {$bulkImport->import_id}");

        // 3. Process the single manual entry
        $entityColumns = $this->getEntityColumnMapping($entityType, $entityId);
        
        $asset = Asset::create(array_merge($validated, $entityColumns, [
            'item_name' => $validated['item_name'],
            'bulk_import_id' => $bulkImport->import_id,
            'serial_number' => $validated['serial_number'] ?? 'AUTO-' . strtoupper(uniqid()),
            'asset_tag' => null, // Initially null to trigger generation
        ]));

        // 4. Update bulk import with results (Mirroring processCsv update)
        $bulkImport->update([
            'total_rows' => 1,
            'successful_imports' => 1,
            'failed_imports' => 0,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // 5. Generate Tag Fallback (Mirroring processCsv logic)
        // This ensures the COM tag is generated immediately
        $missingTags = $bulkImport->assets()->whereNull('asset_tag')->count();
        
        if ($missingTags > 0) {
            Log::info("Generating tag for manual entry asset ID: {$asset->asset_id}");
            // Use your existing logic to generate the specific COM tag
            $this->generateAssetTagsForImport($bulkImport, $entityType);
        }
            Log::info("Asset created with ID: {$asset->asset_id}, bulk_import_id: {$asset->bulk_import_id}");

            // Verifies data was saved
            $savedAsset = Asset::find($asset->asset_id);
        // 6. Final Redirect (Mirroring processCsv show route)
        return redirect()->route('admin.bulk-assets.show', $bulkImport->import_id)
            ->with('success', "Asset added successfully. Asset tag generated.");

    } catch (\Exception $e) {
        Log::error('Manual asset creation failed: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return back()->withInput()->with('error', 'Manual entry failed: ' . $e->getMessage());
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
                'Office Desk',
                'Furniture & Fixtures',
                'office desks', // Must provide subcategory
                'DESK-001',
                '5',
                '85000.00',
                '2024-03-01',
                'available',
                'new',
                'Procurement batch 2024'
            ]);

            fputcsv($file, [
                'HP laser printer 3100',
                'ICT & Electronics',
                'Printers',
                'HP-SN9876578',
                '3',
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
    /**
     * Process CSV/Excel upload
     * FIXED: Tags are now generated inline, no need for bulk generation
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

            Log::info("Created bulk import ID: {$bulkImport->import_id} for entity type: {$entityType}, entity ID: {$entityId}");

            // Process based on file type (tags are generated inline now)
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

            // Check if any tags are still missing (shouldn't happen with inline generation)
            $missingTags = $bulkImport->assets()->whereNull('asset_tag')->count();
            
            if ($missingTags > 0) {
                Log::warning("Found {$missingTags} assets without tags after inline generation. Running bulk generation as fallback.");
                $this->generateAssetTagsForImport($bulkImport, $entityType);
            } else {
                Log::info("All {$result['success']} assets have tags generated successfully.");
            }

            if ($result['success'] > 0) {
                return redirect()->route('admin.bulk-assets.show', $bulkImport->import_id)
                    ->with('success', "Import completed: {$result['success']} successful, {$result['failed']} failed. Asset tags generated.");
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
    /**
     * Process CSV file
     * FIXED: Generate tags immediately after importing each row
     */
    private function processCSVFile($file, $entityType, $entityId, $bulkImport)
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $content = str_replace("\xEF\xBB\xBF", '', $content);
            
            $lines = str_getcsv($content, "\n");
            $csvData = array_map('str_getcsv', $lines);
            
            $csvData = array_filter($csvData, function($row) {
                return !empty(array_filter($row));
            });
            
            $csvData = array_values($csvData);
            
            if (empty($csvData)) {
                throw new \Exception('CSV file is empty or could not be read.');
            }

            $header = array_shift($csvData);
            
            $header = array_map(function($h) {
                $h = trim($h);
                $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);
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
                    if (count($header) !== count($row)) {
                        throw new \Exception("Column mismatch: Expected " . count($header) . " columns, got " . count($row));
                    }

                    $data = array_combine($header, $row);
                    
                    // CRITICAL: importSingleRow now returns the created asset
                    $asset = $this->importSingleRow($data, $entityColumns, $bulkImport->import_id);
                    
                    // CRITICAL: Generate tag immediately after creation
                    if ($asset) {
                        $this->generateAssetTag($asset, $entityType);
                    }
                    
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
     * Process Excel file (.xlsx, .xls)
     * FIXED: Generate tags immediately after each row import
     */
    private function processExcelFile($file, $entityType, $entityId, $bulkImport)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $header = array_shift($rows);
            
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

                if (empty(array_filter($row))) {
                    $total--;
                    continue;
                }

                try {
                    $data = array_combine($header, $row);
                    
                    // CRITICAL: importSingleRow now returns the created asset
                    $asset = $this->importSingleRow($data, $entityColumns, $bulkImport->import_id);
                    
                    // CRITICAL: Generate tag immediately after creation
                    if ($asset) {
                        $this->generateAssetTag($asset, $entityType);
                    }
                    
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
     * Import a single row (used by both CSV and Excel)
     */
    /**
     * Import a single row (used by both CSV and Excel)
     * REQUIRES: Both category AND subcategory must be provided
     */
   /**
     * Import a single row (used by both CSV and Excel)
     * CRITICAL FIX: Ensures bulk_import_id is set and asset tag is generated
     */
    private function importSingleRow($data, $entityColumns, $bulkImportId)
    {
        // Validate category
        $categoryName = trim($data['category_name'] ?? '');
        
        if (empty($categoryName)) {
            throw new \Exception("Category name is required but missing");
        }

        $category = Category::whereRaw('LOWER(category_name) = ?', [strtolower($categoryName)])->first();

        if (!$category) {
            throw new \Exception("Category '{$categoryName}' not found. Available categories: " . 
                Category::pluck('category_name')->implode(', '));
        }

        // Validate subcategory - REQUIRED
        $subcatName = trim($data['subcategory_name'] ?? '');
        
        if (empty($subcatName)) {
            throw new \Exception("Subcategory is required. Please provide a subcategory for category '{$categoryName}'");
        }

        $subcategory = Subcategory::whereRaw('LOWER(subcategory_name) = ?', [strtolower($subcatName)])
            ->where('category_id', $category->category_id)
            ->first();
            
        if (!$subcategory) {
            $availableSubs = Subcategory::where('category_id', $category->category_id)
                ->pluck('subcategory_name')
                ->implode(', ');
            
            if ($availableSubs) {
                throw new \Exception("Subcategory '{$subcatName}' not found for category '{$categoryName}'. Available subcategories: {$availableSubs}");
            } else {
                throw new \Exception("No subcategories found for category '{$categoryName}'. Please add subcategories first.");
            }
        }

        // Handle serial number - check for uniqueness
        $serialNumber = !empty($data['serial_number']) ? trim($data['serial_number']) : null;
        
        if ($serialNumber) {
            $existingAsset = Asset::where('serial_number', $serialNumber)->first();
            
            if ($existingAsset) {
                $originalSerial = $serialNumber;
                $counter = 1;
                
                do {
                    $serialNumber = $originalSerial . '-DUP' . $counter;
                    $counter++;
                    $exists = Asset::where('serial_number', $serialNumber)->exists();
                } while ($exists && $counter < 100);
                
                Log::warning("Duplicate serial number '{$originalSerial}' found. Changed to '{$serialNumber}'");
            }
        } else {
            $serialNumber = 'AUTO-' . strtoupper(uniqid()) . '-' . now()->timestamp;
        }

        // Prepare asset data - CRITICAL: include bulk_import_id
        $assetData = [
            'item_name' => trim($data['item_name'] ?? ''),
            'category_id' => $category->category_id,
            'subcategory_id' => $subcategory->subcategory_id,
            'serial_number' => $serialNumber,
            'asset_tag' => null, // Will be generated immediately after creation
            'quantity' => isset($data['quantity']) && is_numeric($data['quantity']) ? (int)$data['quantity'] : 1,
            'purchase_price' => isset($data['purchase_price']) && is_numeric($data['purchase_price']) ? (float)$data['purchase_price'] : 0,
            'purchase_date' => !empty($data['purchase_date']) ? $this->parseDate($data['purchase_date']) : null,
            'status' => !empty($data['status']) ? strtolower(trim($data['status'])) : 'available',
            'condition' => !empty($data['condition']) ? trim($data['condition']) : null,
            'notes' => $data['notes'] ?? null,
            'bulk_import_id' => $bulkImportId, // CRITICAL: This must be set!
        ];

        // Merge entity columns
        $assetData = array_merge($assetData, $entityColumns);

        // Validation
        $validator = Validator::make($assetData, [
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'subcategory_id' => 'required|exists:subcategories,subcategory_id',
            'serial_number' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'status' => 'required|in:available,assigned,maintenance,retired',
            'bulk_import_id' => 'required|exists:bulk_imports,import_id', // Validate it exists
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        // Create asset
        try {
            $asset = Asset::create($assetData);
            Log::info("Successfully imported asset ID {$asset->asset_id}: {$assetData['item_name']} with serial: {$serialNumber}, bulk_import_id: {$bulkImportId}");
            
            // IMPORTANT: Return the created asset so we can generate tag immediately
            return $asset;
            
        } catch (\Exception $e) {
            Log::error("Failed to create asset: " . $e->getMessage());
            Log::error("Asset data: " . json_encode($assetData));
            throw $e;
        }
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        try {
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd-m-Y', 'm-d-Y'];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            
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
     * CRITICAL: This must load all relationships to work properly
     */
    private function generateAssetTag($asset, $entityType)
    {
        Log::info("========== ASSET TAG GENERATION START ==========");
        Log::info("Asset ID: {$asset->asset_id}");
        Log::info("Entity Type: {$entityType}");
        
        try {
            // CRITICAL: Refresh the asset from database with ALL relationships
            $assetId = $asset->asset_id;
            $asset = Asset::with([
                'unit', 
                'department', 
                'faculty', 
                'office',
                'institute', 
                'category', 
                'subcategory'
            ])->find($assetId);

            if (!$asset) {
                Log::error("Asset {$assetId} not found after refresh!");
                return null;
            }

            Log::info("Asset refreshed with relationships");

            // Debug: Log which entity columns are set
            Log::info("Entity Columns Check:");
            Log::info("- current_unit_id: " . ($asset->current_unit_id ?? 'NULL'));
            Log::info("- current_dept_id: " . ($asset->current_dept_id ?? 'NULL'));
            Log::info("- current_office_id: " . ($asset->current_office_id ?? 'NULL'));
            Log::info("- current_faculty_id: " . ($asset->current_faculty_id ?? 'NULL'));
            Log::info("- current_institute_id: " . ($asset->current_institute_id ?? 'NULL'));

            // Debug: Log relationships
            Log::info("Relationships Check:");
            Log::info("- unit: " . ($asset->unit ? "ID {$asset->unit->unit_id}, Code: {$asset->unit->unit_code}" : 'NULL'));
            Log::info("- department: " . ($asset->department ? "ID {$asset->department->dept_id}, Code: {$asset->department->dept_code}" : 'NULL'));
            Log::info("- faculty: " . ($asset->faculty ? "ID {$asset->faculty->faculty_id}, Code: {$asset->faculty->faculty_code}" : 'NULL'));
            Log::info("- office: " . ($asset->office ? "ID {$asset->office->office_id}, Code: {$asset->office->office_code}" : 'NULL'));
            Log::info("- institute: " . ($asset->institute ? "ID {$asset->institute->institute_id}, Code: {$asset->institute->institute_code}" : 'NULL'));
            Log::info("- category: " . ($asset->category ? "ID {$asset->category->category_id}, Code: {$asset->category->category_code}" : 'NULL'));
            Log::info("- subcategory: " . ($asset->subcategory ? "ID {$asset->subcategory->subcategory_id}, Code: {$asset->subcategory->subcategory_code}" : 'NULL'));

            // Get prefix based on entity type
            $prefix = match($entityType) {
                'office'     => $asset->office?->office_code ?? 'OFF',
                'unit'       => $asset->unit?->unit_code ?? 'UNIT',
                'department' => $asset->department?->dept_code ?? 'DEPT',
                'institute'  => $asset->institute?->institute_code ?? 'INST',
                'faculty'    => $asset->faculty?->faculty_code ?? 'FAC',
                default      => 'COM',
            };

            Log::info("Entity Prefix: {$prefix}");

            $year = $asset->created_at ? $asset->created_at->format('y') : date('y');
            Log::info("Year: {$year}");

            $catCode = $asset->category?->category_code ?? 'XX';
            Log::info("Category Code: {$catCode}");

            $subcatCode = $asset->subcategory?->subcategory_code ?? 'XX';
            Log::info("Subcategory Code: {$subcatCode}");

            $serial = str_pad($asset->asset_id, 6, '0', STR_PAD_LEFT);
            Log::info("Serial: {$serial}");

            $assetTag = "COM/{$prefix}/{$catCode}/{$subcatCode}/{$year}/{$serial}";
            Log::info("Generated Tag: {$assetTag}");

            // Update using DB query to bypass any model issues
            DB::table('assets')
                ->where('asset_id', $asset->asset_id)
                ->update([
                    'asset_tag' => $assetTag,
                    'updated_at' => now()
                ]);

            // Verify the update worked
            $updatedAsset = Asset::find($asset->asset_id);
            Log::info("Verification - Asset tag after update: " . ($updatedAsset->asset_tag ?? 'NULL'));

            if ($updatedAsset->asset_tag === $assetTag) {
                Log::info("✅ Asset tag successfully saved to database");
            } else {
                Log::error("❌ Asset tag was NOT saved to database!");
            }

            Log::info("========== ASSET TAG GENERATION END ==========");
            return $assetTag;

        } catch (\Exception $e) {
            Log::error("========== ASSET TAG GENERATION FAILED ==========");
            Log::error("Error: " . $e->getMessage());
            Log::error("File: " . $e->getFile() . " Line: " . $e->getLine());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Try to save a fallback tag
            try {
                $fallbackTag = 'PENDING_TAG_' . $asset->asset_id;
                DB::table('assets')
                    ->where('asset_id', $asset->asset_id)
                    ->update(['asset_tag' => $fallbackTag]);
                Log::info("Saved fallback tag: {$fallbackTag}");
            } catch (\Exception $fallbackError) {
                Log::error("Failed to save even fallback tag: " . $fallbackError->getMessage());
            }
            
            return null;
        }
    }

    /**
     * Generate asset tags for all assets in an import batch
     * CRITICAL: Load relationships and handle errors gracefully
     */
    private function generateAssetTagsForImport($bulkImport, $entityType)
    {
        Log::info("========================================");
        Log::info("BULK TAG GENERATION START");
        Log::info("Import ID: {$bulkImport->import_id}");
        Log::info("Entity Type: {$entityType}");
        Log::info("========================================");

        // Get count of assets without tags
        $untaggedCount = $bulkImport->assets()->whereNull('asset_tag')->count();
        Log::info("Assets without tags: {$untaggedCount}");

        if ($untaggedCount === 0) {
            Log::warning("No assets found without tags!");
            return 0;
        }

        // Load assets WITHOUT relationships first to see the raw data
        $assetsRaw = $bulkImport->assets()->whereNull('asset_tag')->get();
        Log::info("Raw assets loaded: {$assetsRaw->count()}");

        foreach ($assetsRaw as $index => $rawAsset) {
            Log::info("Asset #{$index}: ID={$rawAsset->asset_id}, current_dept_id={$rawAsset->current_dept_id}, current_unit_id={$rawAsset->current_unit_id}, current_faculty_id={$rawAsset->current_faculty_id}");
        }

        // Now load with relationships
        $assets = $bulkImport->assets()
            ->with(['unit', 'department', 'faculty', 'office', 'institute', 'category', 'subcategory'])
            ->whereNull('asset_tag')
            ->get();
        
        Log::info("Assets loaded with relationships: {$assets->count()}");
        
        $generatedCount = 0;
        $failedCount = 0;

        foreach ($assets as $asset) {
            Log::info("----------------------------------------");
            Log::info("Processing Asset ID: {$asset->asset_id}");
            
            try {
                $result = $this->generateAssetTag($asset, $entityType);
                
                if ($result) {
                    $generatedCount++;
                    Log::info("✅ Success: Tag generated for asset {$asset->asset_id}");
                } else {
                    $failedCount++;
                    Log::error("❌ Failed: No tag returned for asset {$asset->asset_id}");
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("❌ Exception generating tag for asset {$asset->asset_id}: " . $e->getMessage());
            }
        }

        Log::info("========================================");
        Log::info("BULK TAG GENERATION COMPLETE");
        Log::info("Total: {$assets->count()}");
        Log::info("Success: {$generatedCount}");
        Log::info("Failed: {$failedCount}");
        Log::info("========================================");
        
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