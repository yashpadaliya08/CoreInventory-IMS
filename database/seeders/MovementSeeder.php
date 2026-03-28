<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockLedger;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Adjustment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MovementSeeder extends Seeder
{
    public function run(): void
    {
        $dispatchBay  = Location::where('name', 'Dispatch Bay')->first();
        $rackA        = Location::where('name', 'Rack A — Storage')->first();
        $rackB        = Location::where('name', 'Rack B — Storage')->first();
        $coldStorage  = Location::where('name', 'Cold Storage')->first();
        $inboundDock  = Location::where('name', 'Inbound Dock')->first();

        $steelRod     = Product::where('sku', 'RM-STEEL-12')->first();
        $motor        = Product::where('sku', 'FG-EMOT-05')->first();
        $fan          = Product::where('sku', 'FG-IFAN-24')->first();
        $discCutter   = Product::where('sku', 'CON-DISC-09')->first();
        $bubblewrap   = Product::where('sku', 'PKG-BWRAP-01')->first();

        // ── Deliveries ──────────────────────────────────────────────────────
        $deliveries = [
            // Validated outbound delivery — stock deducted from ledger
            [
                'data' => [
                    'reference_no'   => 'DEL-2026-001',
                    'customer_name'  => 'Reliance Industries Ltd',
                    'status'         => 'Done',
                    'scheduled_date' => Carbon::now()->subDays(4)->toDateString(),
                ],
                'items' => [
                    ['product' => $steelRod, 'location' => $dispatchBay, 'qty' => 200],
                ],
                'write_ledger' => true,
            ],
            // Draft delivery — waiting to be dispatched
            [
                'data' => [
                    'reference_no'   => 'DEL-2026-002',
                    'customer_name'  => 'Siemens India',
                    'status'         => 'Draft',
                    'scheduled_date' => Carbon::now()->addDays(2)->toDateString(),
                ],
                'items' => [
                    ['product' => $motor, 'location' => $dispatchBay, 'qty' => 5],
                    ['product' => $fan,   'location' => $dispatchBay, 'qty' => 3],
                ],
                'write_ledger' => false,
            ],
        ];

        foreach ($deliveries as $d) {
            $delivery = Delivery::updateOrCreate(
                ['reference_no' => $d['data']['reference_no']],
                $d['data']
            );
            $delivery->deliveryItems()->delete();

            foreach ($d['items'] as $item) {
                if (!$item['product'] || !$item['location']) continue;

                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'product_id'  => $item['product']->id,
                    'quantity'    => $item['qty'],
                ]);

                if ($d['write_ledger']) {
                    StockLedger::create([
                        'product_id'      => $item['product']->id,
                        'location_id'     => $item['location']->id,
                        'reference_type'  => Delivery::class,
                        'reference_id'    => $delivery->id,
                        'quantity_change' => -$item['qty'], // Negative — stock leaves
                    ]);
                }
            }
        }

        // ── Transfers ───────────────────────────────────────────────────────
        $transfer = Transfer::updateOrCreate(
            ['reference_no' => 'TRF-2026-001'],
            [
                'reference_no'     => 'TRF-2026-001',
                'from_location_id' => $inboundDock?->id,
                'to_location_id'   => $coldStorage?->id,
                'status'           => 'Done',
            ]
        );
        $transfer->transferItems()->delete();

        if ($discCutter && $inboundDock && $coldStorage) {
            TransferItem::create([
                'transfer_id' => $transfer->id,
                'product_id'  => $discCutter->id,
                'quantity'    => 100,
            ]);
            // Deduct from source
            StockLedger::create([
                'product_id'      => $discCutter->id,
                'location_id'     => $inboundDock->id,
                'reference_type'  => Transfer::class,
                'reference_id'    => $transfer->id,
                'quantity_change' => -100,
            ]);
            // Credit to destination
            StockLedger::create([
                'product_id'      => $discCutter->id,
                'location_id'     => $coldStorage->id,
                'reference_type'  => Transfer::class,
                'reference_id'    => $transfer->id,
                'quantity_change' => +100,
            ]);
        }

        // ── Adjustment (Stock Count Correction) ─────────────────────────────
        if ($bubblewrap && $rackA) {
            $adjustment = Adjustment::updateOrCreate(
                ['reference_no' => 'ADJ-2026-001'],
                [
                    'reference_no'       => 'ADJ-2026-001',
                    'location_id'        => $rackA->id,
                    'product_id'         => $bubblewrap->id,
                    'recorded_quantity'  => 50,
                    'physical_quantity'  => 42,
                    'difference_quantity'=> -8,
                    'status'             => 'Done',
                ]
            );

            // Write shrinkage to ledger
            StockLedger::updateOrCreate(
                ['reference_type' => Adjustment::class, 'reference_id' => $adjustment->id],
                [
                    'product_id'      => $bubblewrap->id,
                    'location_id'     => $rackA->id,
                    'reference_type'  => Adjustment::class,
                    'reference_id'    => $adjustment->id,
                    'quantity_change' => -8,
                ]
            );
        }

        $this->command->info('  ✔ Movements seeded: 2 deliveries, 1 transfer, 1 stock adjustment');
    }
}
