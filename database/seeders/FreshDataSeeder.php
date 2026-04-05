<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Adjustment;
use App\Models\StockLedger;

class FreshDataSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────
        // PHASE 1: WIPE ALL DATA
        // ──────────────────────────────────────
        $this->command->info('🗑️  Wiping all existing data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('activity_log')->truncate();
        DB::table('stock_ledger')->truncate();
        DB::table('adjustments')->truncate();
        DB::table('transfer_items')->truncate();
        DB::table('transfers')->truncate();
        DB::table('delivery_items')->truncate();
        DB::table('deliveries')->truncate();
        DB::table('receipt_items')->truncate();
        DB::table('receipts')->truncate();
        DB::table('purchase_order_items')->truncate();
        DB::table('purchase_orders')->truncate();
        DB::table('vendors')->truncate();
        DB::table('products')->truncate();
        DB::table('locations')->truncate();
        DB::table('warehouses')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ──────────────────────────────────────
        // PHASE 2: USERS
        // ──────────────────────────────────────
        $this->command->info('👤 Seeding users...');

        User::create(['name' => 'Rajesh Kumar',  'email' => 'admin@coreinventory.local',   'password' => Hash::make('Admin@12345'),   'role' => 'admin']);
        User::create(['name' => 'Priya Sharma',  'email' => 'manager@coreinventory.local', 'password' => Hash::make('Manager@12345'), 'role' => 'manager']);
        User::create(['name' => 'Amit Patel',    'email' => 'staff@coreinventory.local',   'password' => Hash::make('Staff@12345'),   'role' => 'staff']);

        // ──────────────────────────────────────
        // PHASE 3: WAREHOUSES & LOCATIONS
        // ──────────────────────────────────────
        $this->command->info('🏭 Seeding warehouses & locations...');

        $whMain = Warehouse::create(['name' => 'Central Distribution Hub', 'code' => 'CDH-01']);
        $whSat  = Warehouse::create(['name' => 'South Zone Satellite',     'code' => 'SZS-02']);
        $whCold = Warehouse::create(['name' => 'Cold Storage Facility',    'code' => 'CSF-03']);

        $locMainStore = Location::create(['name' => 'Main Storage Hall',    'warehouse_id' => $whMain->id, 'type' => 'internal']);
        $locReceiving = Location::create(['name' => 'Receiving Dock',       'warehouse_id' => $whMain->id, 'type' => 'internal']);
        $locDispatch  = Location::create(['name' => 'Dispatch Bay',         'warehouse_id' => $whMain->id, 'type' => 'internal']);
        $locHeavy     = Location::create(['name' => 'Heavy Materials Zone', 'warehouse_id' => $whMain->id, 'type' => 'internal']);
        $locSouthA    = Location::create(['name' => 'Aisle A',              'warehouse_id' => $whSat->id,  'type' => 'internal']);
        $locSouthB    = Location::create(['name' => 'Aisle B',              'warehouse_id' => $whSat->id,  'type' => 'internal']);
        $locColdUnit  = Location::create(['name' => 'Refrigeration Unit',   'warehouse_id' => $whCold->id, 'type' => 'internal']);

        // ──────────────────────────────────────
        // PHASE 4: PRODUCTS (25 realistic items)
        // ──────────────────────────────────────
        $this->command->info('📦 Seeding 25 products...');

        $products = [
            // Construction Materials
            ['name' => 'Portland Cement (OPC 53)',     'sku' => 'MAT-CEM-001', 'category' => 'Construction Materials', 'unit_of_measure' => 'Bags',   'reorder_level' => 200, 'unit_cost' => 380.00,  'selling_price' => 420.00],
            ['name' => 'TMT Steel Bars 12mm',          'sku' => 'MAT-STL-002', 'category' => 'Construction Materials', 'unit_of_measure' => 'Tonnes', 'reorder_level' => 15,  'unit_cost' => 52000.00,'selling_price' => 58500.00],
            ['name' => 'M-Sand (Manufactured Sand)',    'sku' => 'MAT-SND-003', 'category' => 'Construction Materials', 'unit_of_measure' => 'Cu.Ft.', 'reorder_level' => 500, 'unit_cost' => 45.00,   'selling_price' => 62.00],
            ['name' => 'Red Clay Bricks',              'sku' => 'MAT-BRK-004', 'category' => 'Construction Materials', 'unit_of_measure' => 'Units',  'reorder_level' => 5000,'unit_cost' => 8.50,    'selling_price' => 12.00],
            ['name' => 'Aggregate 20mm',               'sku' => 'MAT-AGG-005', 'category' => 'Construction Materials', 'unit_of_measure' => 'Cu.Ft.', 'reorder_level' => 300, 'unit_cost' => 38.00,   'selling_price' => 52.00],
            // Electrical Supplies
            ['name' => 'Copper Wire 2.5mm (Finolex)',  'sku' => 'ELC-COP-001', 'category' => 'Electrical Supplies',   'unit_of_measure' => 'Meters', 'reorder_level' => 1000,'unit_cost' => 18.50,   'selling_price' => 26.00],
            ['name' => 'MCB Switch 32A (Havells)',      'sku' => 'ELC-MCB-002', 'category' => 'Electrical Supplies',   'unit_of_measure' => 'Units',  'reorder_level' => 100, 'unit_cost' => 285.00,  'selling_price' => 380.00],
            ['name' => 'PVC Conduit Pipe 25mm',        'sku' => 'ELC-PVC-003', 'category' => 'Electrical Supplies',   'unit_of_measure' => 'Meters', 'reorder_level' => 500, 'unit_cost' => 22.00,   'selling_price' => 35.00],
            ['name' => 'LED Panel Light 18W',          'sku' => 'ELC-LED-004', 'category' => 'Electrical Supplies',   'unit_of_measure' => 'Units',  'reorder_level' => 50,  'unit_cost' => 320.00,  'selling_price' => 480.00],
            ['name' => 'Distribution Board 8-Way',     'sku' => 'ELC-DIS-005', 'category' => 'Electrical Supplies',   'unit_of_measure' => 'Units',  'reorder_level' => 20,  'unit_cost' => 1250.00, 'selling_price' => 1800.00],
            // Plumbing & Sanitary
            ['name' => 'CPVC Pipe 1 Inch (Astral)',    'sku' => 'PLM-CPV-001', 'category' => 'Plumbing & Sanitary',   'unit_of_measure' => 'Meters', 'reorder_level' => 300, 'unit_cost' => 75.00,   'selling_price' => 110.00],
            ['name' => 'Ball Valve 1/2 Inch (Brass)',  'sku' => 'PLM-BLV-002', 'category' => 'Plumbing & Sanitary',   'unit_of_measure' => 'Units',  'reorder_level' => 80,  'unit_cost' => 145.00,  'selling_price' => 220.00],
            ['name' => 'Water Tank 1000L (Sintex)',     'sku' => 'PLM-TNK-003', 'category' => 'Plumbing & Sanitary',   'unit_of_measure' => 'Units',  'reorder_level' => 5,   'unit_cost' => 6500.00, 'selling_price' => 8200.00],
            ['name' => 'GI Pipe 2 Inch',              'sku' => 'PLM-GIP-004', 'category' => 'Plumbing & Sanitary',   'unit_of_measure' => 'Meters', 'reorder_level' => 150, 'unit_cost' => 210.00,  'selling_price' => 290.00],
            ['name' => 'Teflon Tape Roll',             'sku' => 'PLM-TEF-005', 'category' => 'Plumbing & Sanitary',   'unit_of_measure' => 'Rolls',  'reorder_level' => 200, 'unit_cost' => 12.00,   'selling_price' => 20.00],
            // Safety Equipment
            ['name' => 'Hard Hat (Yellow, ISI)',        'sku' => 'SAF-HAT-001', 'category' => 'Safety Equipment',      'unit_of_measure' => 'Units',  'reorder_level' => 50,  'unit_cost' => 180.00,  'selling_price' => 280.00],
            ['name' => 'Safety Goggles (Anti-Fog)',    'sku' => 'SAF-GOG-002', 'category' => 'Safety Equipment',      'unit_of_measure' => 'Units',  'reorder_level' => 40,  'unit_cost' => 95.00,   'selling_price' => 160.00],
            ['name' => 'Leather Work Gloves',          'sku' => 'SAF-GLV-003', 'category' => 'Safety Equipment',      'unit_of_measure' => 'Pairs',  'reorder_level' => 60,  'unit_cost' => 120.00,  'selling_price' => 195.00],
            ['name' => 'Hi-Vis Safety Vest',           'sku' => 'SAF-VST-004', 'category' => 'Safety Equipment',      'unit_of_measure' => 'Units',  'reorder_level' => 30,  'unit_cost' => 150.00,  'selling_price' => 250.00],
            ['name' => 'First Aid Kit (Industrial)',    'sku' => 'SAF-FAK-005', 'category' => 'Safety Equipment',      'unit_of_measure' => 'Kits',   'reorder_level' => 10,  'unit_cost' => 850.00,  'selling_price' => 1200.00],
            // Tools & Hardware
            ['name' => 'DeWalt Hammer Drill 20V',      'sku' => 'TLS-DRL-001', 'category' => 'Tools & Hardware',      'unit_of_measure' => 'Units',  'reorder_level' => 8,   'unit_cost' => 8500.00, 'selling_price' => 11500.00],
            ['name' => 'Measuring Tape 50m (Stanley)', 'sku' => 'TLS-TAP-002', 'category' => 'Tools & Hardware',      'unit_of_measure' => 'Units',  'reorder_level' => 25,  'unit_cost' => 650.00,  'selling_price' => 950.00],
            ['name' => 'Angle Grinder 4 Inch (Bosch)', 'sku' => 'TLS-GRD-003', 'category' => 'Tools & Hardware',      'unit_of_measure' => 'Units',  'reorder_level' => 10,  'unit_cost' => 3200.00, 'selling_price' => 4500.00],
            ['name' => 'Hex Bolt Set (SS, M8-M16)',    'sku' => 'TLS-BLT-004', 'category' => 'Tools & Hardware',      'unit_of_measure' => 'Sets',   'reorder_level' => 30,  'unit_cost' => 420.00,  'selling_price' => 620.00],
            ['name' => 'Welding Electrode 3.15mm',     'sku' => 'TLS-WEL-005', 'category' => 'Tools & Hardware',      'unit_of_measure' => 'Kg',     'reorder_level' => 50,  'unit_cost' => 180.00,  'selling_price' => 260.00],
        ];

        $p = [];
        foreach ($products as $item) { $p[] = Product::create($item); }

        // ──────────────────────────────────────
        // PHASE 5: VENDORS
        // ──────────────────────────────────────
        $this->command->info('🏢 Seeding 6 vendors...');

        $v = [];
        $v[] = Vendor::create(['name' => 'UltraTech Cement Ltd.',   'contact_person' => 'Vikram Desai',    'email' => 'orders@ultratech.co.in',   'phone' => '+91 22 6692 8000', 'address' => 'Plot 137, GIDC Ankleshwar, Gujarat 393002']);
        $v[] = Vendor::create(['name' => 'Tata Steel Distributors', 'contact_person' => 'Sunil Mehta',     'email' => 'supply@tatasteel.com',      'phone' => '+91 657 266 3817', 'address' => 'Jamshedpur Works, Jharkhand 831001']);
        $v[] = Vendor::create(['name' => 'Havells India Ltd.',      'contact_person' => 'Neha Agarwal',    'email' => 'b2b@havells.com',           'phone' => '+91 120 4771 000', 'address' => 'QRG Towers, Sector 126, Noida 201304']);
        $v[] = Vendor::create(['name' => 'Astral Pipes Pvt. Ltd.', 'contact_person' => 'Rahul Jain',      'email' => 'wholesale@astralpipes.com', 'phone' => '+91 79 2582 1727', 'address' => 'Santej-Kadi Highway, Ahmedabad 382721']);
        $v[] = Vendor::create(['name' => 'Karam Safety Products',  'contact_person' => 'Deepak Malhotra', 'email' => 'sales@kaborasafety.in',     'phone' => '+91 172 265 3663', 'address' => 'Industrial Area Phase II, Chandigarh 160002']);
        $v[] = Vendor::create(['name' => 'Bosch Power Tools India','contact_person' => 'Meera Kulkarni',  'email' => 'dealer@bosch.co.in',        'phone' => '+91 80 2299 2376', 'address' => 'Hosur Road, Adugodi, Bangalore 560030']);

        // ──────────────────────────────────────
        // PHASE 6: PURCHASE ORDERS
        // ──────────────────────────────────────
        $this->command->info('📋 Seeding purchase orders...');

        $po1 = PurchaseOrder::create(['reference_no' => 'PO-2026-0001', 'vendor_id' => $v[0]->id, 'status' => 'Approved', 'expected_date' => now()->subDays(5), 'notes' => 'Q1 bulk cement procurement for Andheri project']);
        PurchaseOrderItem::create(['purchase_order_id' => $po1->id, 'product_id' => $p[0]->id, 'quantity' => 500, 'unit_cost' => 380.00]);
        PurchaseOrderItem::create(['purchase_order_id' => $po1->id, 'product_id' => $p[3]->id, 'quantity' => 10000, 'unit_cost' => 8.50]);

        $po2 = PurchaseOrder::create(['reference_no' => 'PO-2026-0002', 'vendor_id' => $v[1]->id, 'status' => 'Approved', 'expected_date' => now()->subDays(3), 'notes' => 'Monthly steel bar replenishment']);
        PurchaseOrderItem::create(['purchase_order_id' => $po2->id, 'product_id' => $p[1]->id, 'quantity' => 25, 'unit_cost' => 52000.00]);
        PurchaseOrderItem::create(['purchase_order_id' => $po2->id, 'product_id' => $p[4]->id, 'quantity' => 800, 'unit_cost' => 38.00]);

        $po3 = PurchaseOrder::create(['reference_no' => 'PO-2026-0003', 'vendor_id' => $v[2]->id, 'status' => 'Draft', 'expected_date' => now()->addDays(7), 'notes' => 'Electrical supplies for Block-C wiring']);
        PurchaseOrderItem::create(['purchase_order_id' => $po3->id, 'product_id' => $p[5]->id, 'quantity' => 2000, 'unit_cost' => 18.50]);
        PurchaseOrderItem::create(['purchase_order_id' => $po3->id, 'product_id' => $p[6]->id, 'quantity' => 150, 'unit_cost' => 285.00]);
        PurchaseOrderItem::create(['purchase_order_id' => $po3->id, 'product_id' => $p[8]->id, 'quantity' => 80, 'unit_cost' => 320.00]);

        $po4 = PurchaseOrder::create(['reference_no' => 'PO-2026-0004', 'vendor_id' => $v[4]->id, 'status' => 'Approved', 'expected_date' => now()->subDays(8), 'notes' => 'Safety compliance equipment order']);
        PurchaseOrderItem::create(['purchase_order_id' => $po4->id, 'product_id' => $p[15]->id, 'quantity' => 100, 'unit_cost' => 180.00]);
        PurchaseOrderItem::create(['purchase_order_id' => $po4->id, 'product_id' => $p[16]->id, 'quantity' => 80, 'unit_cost' => 95.00]);
        PurchaseOrderItem::create(['purchase_order_id' => $po4->id, 'product_id' => $p[17]->id, 'quantity' => 120, 'unit_cost' => 120.00]);
        PurchaseOrderItem::create(['purchase_order_id' => $po4->id, 'product_id' => $p[18]->id, 'quantity' => 60, 'unit_cost' => 150.00]);

        // ──────────────────────────────────────
        // PHASE 7: RECEIPTS (validated → stock in)
        // ──────────────────────────────────────
        $this->command->info('📥 Seeding receipts & inbound stock...');

        // Helper function to create a validated receipt with ledger entries
        $makeReceipt = function (string $ref, ?int $vendorId, string $vendorName, string $status, int $daysAgo, array $items, int $locationId) use (&$p) {
            $r = Receipt::create([
                'reference_no' => $ref,
                'vendor_id' => $vendorId,
                'vendor_name' => $vendorName,
                'status' => $status,
                'expected_date' => $daysAgo > 0 ? now()->subDays($daysAgo) : now()->addDays(abs($daysAgo)),
            ]);
            foreach ($items as [$productIdx, $qty]) {
                ReceiptItem::create(['receipt_id' => $r->id, 'product_id' => $p[$productIdx]->id, 'quantity' => $qty]);
                if ($status === 'Done') {
                    StockLedger::create(['product_id' => $p[$productIdx]->id, 'location_id' => $locationId, 'reference_type' => Receipt::class, 'reference_id' => $r->id, 'quantity_change' => $qty]);
                }
            }
            return $r;
        };

        $makeReceipt('REC-2026-0001', $v[0]->id, 'UltraTech Cement Ltd.',   'Done', 4,  [[0, 500], [3, 10000]],                               $locMainStore->id);
        $makeReceipt('REC-2026-0002', $v[1]->id, 'Tata Steel Distributors', 'Done', 2,  [[1, 25], [4, 800]],                                    $locHeavy->id);
        $makeReceipt('REC-2026-0003', $v[4]->id, 'Karam Safety Products',   'Done', 6,  [[15, 100], [16, 80], [17, 120], [18, 60]],              $locMainStore->id);
        $makeReceipt('REC-2026-0004', $v[2]->id, 'Havells India Ltd.',      'Done', 10, [[5, 1500], [6, 200], [7, 800], [8, 60], [9, 30]],       $locSouthA->id);
        $makeReceipt('REC-2026-0005', $v[3]->id, 'Astral Pipes Pvt. Ltd.', 'Done', 12, [[10, 600], [11, 150], [12, 12], [13, 300], [14, 500]],  $locMainStore->id);
        $makeReceipt('REC-2026-0006', $v[5]->id, 'Bosch Power Tools India', 'Done', 15, [[20, 15], [21, 40], [22, 20], [23, 50], [24, 100]],    $locSouthB->id);
        $makeReceipt('REC-2026-0007', $v[0]->id, 'UltraTech Cement Ltd.',   'Draft', -3, [[0, 300], [2, 1000]],                                  $locMainStore->id);
        $makeReceipt('REC-2026-0008', null,       'Local Sand Supplier',     'Done', 7,  [[2, 800]],                                              $locHeavy->id);
        $makeReceipt('REC-2026-0009', $v[4]->id, 'Karam Safety Products',   'Done', 9,  [[19, 15]],                                              $locMainStore->id);

        // ──────────────────────────────────────
        // PHASE 8: DELIVERIES (outbound)
        // ──────────────────────────────────────
        $this->command->info('📤 Seeding deliveries...');

        $makeDelivery = function (string $ref, string $customer, string $status, int $daysAgo, array $items, int $locationId) use (&$p) {
            $d = Delivery::create([
                'reference_no' => $ref,
                'customer_name' => $customer,
                'status' => $status,
                'scheduled_date' => $daysAgo > 0 ? now()->subDays($daysAgo) : now()->addDays(abs($daysAgo)),
            ]);
            foreach ($items as [$productIdx, $qty]) {
                DeliveryItem::create(['delivery_id' => $d->id, 'product_id' => $p[$productIdx]->id, 'quantity' => $qty]);
                if ($status === 'Done') {
                    StockLedger::create(['product_id' => $p[$productIdx]->id, 'location_id' => $locationId, 'reference_type' => Delivery::class, 'reference_id' => $d->id, 'quantity_change' => -$qty]);
                }
            }
        };

        $makeDelivery('DEL-2026-0001', 'L&T Construction — Andheri Site',        'Done',  2, [[0, 150], [3, 3000]],                    $locMainStore->id);
        $makeDelivery('DEL-2026-0002', 'Shapoorji Pallonji — BKC Tower',         'Done',  1, [[15, 40], [16, 30], [17, 50], [18, 20]], $locMainStore->id);
        $makeDelivery('DEL-2026-0003', 'Godrej Properties — Vikhroli',           'Draft', -2, [[1, 5], [10, 200]],                     $locMainStore->id);
        $makeDelivery('DEL-2026-0004', 'Oberoi Realty — Goregaon',               'Done',  3, [[5, 400], [7, 200]],                     $locSouthA->id);

        // ──────────────────────────────────────
        // PHASE 9: TRANSFERS
        // ──────────────────────────────────────
        $this->command->info('🔄 Seeding transfers...');

        $t1 = Transfer::create(['reference_no' => 'TRF-2026-0001', 'from_location_id' => $locMainStore->id, 'to_location_id' => $locSouthA->id, 'status' => 'Done']);
        foreach ([[0, 100], [14, 100]] as [$idx, $qty]) {
            TransferItem::create(['transfer_id' => $t1->id, 'product_id' => $p[$idx]->id, 'quantity' => $qty]);
            StockLedger::create(['product_id' => $p[$idx]->id, 'location_id' => $locMainStore->id, 'reference_type' => Transfer::class, 'reference_id' => $t1->id, 'quantity_change' => -$qty]);
            StockLedger::create(['product_id' => $p[$idx]->id, 'location_id' => $locSouthA->id,    'reference_type' => Transfer::class, 'reference_id' => $t1->id, 'quantity_change' => $qty]);
        }

        $t2 = Transfer::create(['reference_no' => 'TRF-2026-0002', 'from_location_id' => $locSouthB->id, 'to_location_id' => $locDispatch->id, 'status' => 'Done']);
        foreach ([[21, 10], [23, 15]] as [$idx, $qty]) {
            TransferItem::create(['transfer_id' => $t2->id, 'product_id' => $p[$idx]->id, 'quantity' => $qty]);
            StockLedger::create(['product_id' => $p[$idx]->id, 'location_id' => $locSouthB->id,  'reference_type' => Transfer::class, 'reference_id' => $t2->id, 'quantity_change' => -$qty]);
            StockLedger::create(['product_id' => $p[$idx]->id, 'location_id' => $locDispatch->id, 'reference_type' => Transfer::class, 'reference_id' => $t2->id, 'quantity_change' => $qty]);
        }

        // ──────────────────────────────────────
        // PHASE 10: ADJUSTMENTS
        // ──────────────────────────────────────
        $this->command->info('⚖️  Seeding adjustments...');

        $a1 = Adjustment::create(['reference_no' => 'ADJ-2026-0001', 'location_id' => $locMainStore->id, 'product_id' => $p[3]->id, 'recorded_quantity' => 7000, 'physical_quantity' => 6985, 'difference_quantity' => -15, 'status' => 'Done']);
        StockLedger::create(['product_id' => $p[3]->id, 'location_id' => $locMainStore->id, 'reference_type' => Adjustment::class, 'reference_id' => $a1->id, 'quantity_change' => -15]);

        $a2 = Adjustment::create(['reference_no' => 'ADJ-2026-0002', 'location_id' => $locMainStore->id, 'product_id' => $p[14]->id, 'recorded_quantity' => 300, 'physical_quantity' => 308, 'difference_quantity' => 8, 'status' => 'Done']);
        StockLedger::create(['product_id' => $p[14]->id, 'location_id' => $locMainStore->id, 'reference_type' => Adjustment::class, 'reference_id' => $a2->id, 'quantity_change' => 8]);

        $a3 = Adjustment::create(['reference_no' => 'ADJ-2026-0003', 'location_id' => $locSouthB->id, 'product_id' => $p[22]->id, 'recorded_quantity' => 20, 'physical_quantity' => 18, 'difference_quantity' => -2, 'status' => 'Done']);
        StockLedger::create(['product_id' => $p[22]->id, 'location_id' => $locSouthB->id, 'reference_type' => Adjustment::class, 'reference_id' => $a3->id, 'quantity_change' => -2]);

        // ──────────────────────────────────────
        // DONE
        // ──────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Fresh IMS data seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   3 Users  |  3 Warehouses  |  7 Locations');
        $this->command->info('   25 Products  |  6 Vendors  |  4 Purchase Orders');
        $this->command->info('   9 Receipts  |  4 Deliveries  |  2 Transfers  |  3 Adjustments');
        $this->command->info('');
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('   admin@coreinventory.local   → Admin@12345');
        $this->command->info('   manager@coreinventory.local → Manager@12345');
        $this->command->info('   staff@coreinventory.local   → Staff@12345');
    }
}
