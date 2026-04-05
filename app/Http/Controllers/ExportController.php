<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\LedgerExport;
use App\Exports\VendorsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export all products as an Excel (.xlsx) file.
     */
    public function products()
    {
        return Excel::download(
            new ProductsExport(),
            'CoreInventory_Products_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export all products as a CSV file.
     */
    public function productsCsv()
    {
        return Excel::download(
            new ProductsExport(),
            'CoreInventory_Products_' . date('Y-m-d') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Export the stock ledger as an Excel (.xlsx) file.
     */
    public function ledger()
    {
        return Excel::download(
            new LedgerExport(),
            'CoreInventory_StockLedger_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export the stock ledger as a CSV file.
     */
    public function ledgerCsv()
    {
        return Excel::download(
            new LedgerExport(),
            'CoreInventory_StockLedger_' . date('Y-m-d') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Export all vendors as an Excel (.xlsx) file.
     */
    public function vendors()
    {
        return Excel::download(
            new VendorsExport(),
            'CoreInventory_Vendors_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export all vendors as a CSV file.
     */
    public function vendorsCsv()
    {
        return Excel::download(
            new VendorsExport(),
            'CoreInventory_Vendors_' . date('Y-m-d') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
