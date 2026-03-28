<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\StockLedger;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReceiptSeeder extends Seeder
{
    public function run(): void
    {
        // Get the Receiving Bay location (input type)
        $receivingBay  = Location::where('name', 'Receiving Bay')->first();
        $inboundDock   = Location::where('name', 'Inbound Dock')->first();

        $steelRod      = Product::where('sku', 'RM-STEEL-12')->first();
        $aluSheet      = Product::where('sku', 'RM-ALU-03')->first();
        $copperWire    = Product::where('sku', 'RM-COP-15')->first();
        $weldingRods   = Product::where('sku', 'CON-WELD-01')->first();
        $safetyGloves  = Product::where('sku', 'CON-GLOVE-01')->first();
        $cardboardBox  = Product::where('sku', 'PKG-BOX-LG')->first();

        $receipts = [
            // Validated receipt — stock already in ledger
            [
                'data' => [
                    'reference_no'  => 'RCT-2026-001',
                    'vendor_name'   => 'Tata Steel Suppliers',
                    'status'        => 'Done',
                    'expected_date' => Carbon::now()->subDays(10)->toDateString(),
                ],
                'items' => [
                    ['product' => $steelRod,   'location' => $receivingBay,  'qty' => 800],
                    ['product' => $aluSheet,   'location' => $receivingBay,  'qty' => 150],
                ],
                'write_ledger' => true,
            ],
            // Validated receipt — secondary hub
            [
                'data' => [
                    'reference_no'  => 'RCT-2026-002',
                    'vendor_name'   => 'Bharat Consumables',
                    'status'        => 'Done',
                    'expected_date' => Carbon::now()->subDays(5)->toDateString(),
                ],
                'items' => [
                    ['product' => $weldingRods,  'location' => $inboundDock,  'qty' => 60],
                    ['product' => $safetyGloves, 'location' => $inboundDock,  'qty' => 120],
                    ['product' => $cardboardBox, 'location' => $inboundDock,  'qty' => 400],
                ],
                'write_ledger' => true,
            ],
            // Pending receipt — draft, awaiting physical arrival
            [
                'data' => [
                    'reference_no'  => 'RCT-2026-003',
                    'vendor_name'   => 'Gujarat Wire Industries',
                    'status'        => 'Waiting',
                    'expected_date' => Carbon::now()->addDays(3)->toDateString(),
                ],
                'items' => [
                    ['product' => $copperWire,  'location' => $receivingBay, 'qty' => 300],
                ],
                'write_ledger' => false,
            ],
        ];

        foreach ($receipts as $r) {
            $receipt = Receipt::updateOrCreate(
                ['reference_no' => $r['data']['reference_no']],
                $r['data']
            );

            // Clear old items if re-seeding
            $receipt->receiptItems()->delete();

            foreach ($r['items'] as $item) {
                if (!$item['product'] || !$item['location']) continue;

                $receiptItem = ReceiptItem::create([
                    'receipt_id'  => $receipt->id,
                    'product_id'  => $item['product']->id,
                    'quantity'    => $item['qty'],
                ]);

                // Only write ledger for validated (Done) receipts
                if ($r['write_ledger']) {
                    StockLedger::create([
                        'product_id'      => $item['product']->id,
                        'location_id'     => $item['location']->id,
                        'reference_type'  => Receipt::class,
                        'reference_id'    => $receipt->id,
                        'quantity_change' => $item['qty'],
                    ]);
                }
            }
        }

        $this->command->info('  ✔ Receipts seeded: 2 Done, 1 Waiting — ledger updated');
    }
}
