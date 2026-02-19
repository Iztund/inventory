<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetsImport implements ToModel, WithHeadingRow
{
    protected $meta;

    public function __construct(array $meta)
    {
        $this->meta = $meta; // Passes entity_type, entity_id, etc.
    }

    public function model(array $row)
    {
        return new Asset([
            'item_name'            => $row['item_name'],
            'category_id'          => $row['category_id'],
            'subcategory_id'       => $row['subcategory_id'],
            'serial_number'        => $row['serial_number'] ?? null,
            'purchase_price'       => $row['price'] ?? 0,
            'purchase_date'        => isset($row['date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']) : now(),
            'quantity'             => $row['quantity'] ?? 1,
            'status'               => 'available',
            // Set organizational ownership from the controller data
            "current_{$this->meta['type']}_id" => $this->meta['id'],
        ]);
    }
}