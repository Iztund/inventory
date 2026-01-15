<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrganizationalStructureExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($summary)
    {
        $this->data = $summary;
    }

    public function collection()
    {
        return collect([
            ['Academic: Faculties', $this->data['total_faculties']],
            ['Academic: Departments', $this->data['total_departments']],
            ['Admin: Offices', $this->data['total_offices']],
            ['Admin: Functional Units', $this->data['total_units']],
            ['Specialized: Institutes', $this->data['total_institutes']],
        ]);
    }

    public function headings(): array
    {
        return [
            ['ORGANIZATIONAL STRUCTURE REPORT'], // Row 1: Title
            ['Generated on: ' . now()->format('Y-m-d H:i')], // Row 2: Date
            [], // Row 3: Spacer
            ['Classification', 'Total Count'] // Row 4: Actual Headers
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the main Title (Row 1)
            1    => ['font' => ['bold' => true, 'size' => 16]],
            
            // Style the Table Headers (Row 4)
            4    => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'] // Matches your btn-dark color
                ]
            ],
        ];
    }
}
