<?php

namespace App\Exports;

use App\Models\StockLedger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * Fetch all stock ledger entries with related product and location.
     */
    public function collection()
    {
        return StockLedger::with(['product', 'location'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Define the column headings.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Timestamp',
            'Product Name',
            'SKU',
            'Location',
            'Quantity Change',
            'Movement Type',
            'Reference ID',
        ];
    }

    /**
     * Map each ledger entry to the columns.
     */
    public function map($ledger): array
    {
        return [
            $ledger->id,
            $ledger->created_at?->format('Y-m-d H:i:s'),
            $ledger->product->name ?? 'Unknown',
            $ledger->product->sku ?? 'N/A',
            $ledger->location->name ?? 'Unknown',
            $ledger->quantity_change,
            class_basename($ledger->reference_type),
            $ledger->reference_id,
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
