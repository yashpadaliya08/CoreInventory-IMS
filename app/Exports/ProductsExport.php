<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * Fetch all active (non-deleted) products.
     */
    public function collection()
    {
        return Product::orderBy('name')->get();
    }

    /**
     * Define the column headings for the CSV / Excel sheet.
     */
    public function headings(): array
    {
        return [
            'ID',
            'SKU',
            'Product Name',
            'Category',
            'Unit of Measure',
            'Reorder Level',
            'Unit Cost (₹)',
            'Selling Price (₹)',
            'Profit Margin (%)',
            'Total Stock',
            'Stock Status',
            'Created At',
        ];
    }

    /**
     * Map each product row to the columns defined above.
     */
    public function map($product): array
    {
        $totalStock = $product->total_stock;
        $isLow = $totalStock < $product->reorder_level;

        return [
            $product->id,
            $product->sku,
            $product->name,
            $product->category ?? 'Uncategorized',
            $product->unit_of_measure,
            $product->reorder_level,
            number_format($product->unit_cost, 2),
            number_format($product->selling_price, 2),
            $product->profit_margin . '%',
            $totalStock,
            $isLow ? 'LOW STOCK' : 'Healthy',
            $product->created_at?->format('Y-m-d H:i'),
        ];
    }

    /**
     * Style the header row (bold, colored background).
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
