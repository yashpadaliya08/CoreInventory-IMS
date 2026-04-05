<?php

namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * Fetch all active vendors with their purchase order count.
     */
    public function collection()
    {
        return Vendor::withCount('purchaseOrders')
            ->orderBy('name')
            ->get();
    }

    /**
     * Define the column headings.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Company Name',
            'Contact Person',
            'Email',
            'Phone',
            'Address',
            'Total POs',
            'Added On',
        ];
    }

    /**
     * Map each vendor to row columns.
     */
    public function map($vendor): array
    {
        return [
            $vendor->id,
            $vendor->name,
            $vendor->contact_person ?? '-',
            $vendor->email ?? '-',
            $vendor->phone ?? '-',
            $vendor->address ?? '-',
            $vendor->purchase_orders_count,
            $vendor->created_at?->format('Y-m-d'),
        ];
    }

    /**
     * Style the header row.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6366F1'],
                ],
            ],
        ];
    }
}
