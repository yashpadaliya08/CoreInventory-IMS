<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'category', 'unit_cost', 'selling_price', 'reorder_level'])
            ->logOnlyDirty()
            ->useLogName('product');
    }

    // SoftDeletes requires timestamps — Product now tracks created_at/updated_at/deleted_at
    public $timestamps = true;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'unit_of_measure',
        'reorder_level',
        'unit_cost',
        'selling_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost'     => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function stockLedgerEntries(): HasMany
    {
        return $this->hasMany(StockLedger::class);
    }

    public function receiptItems(): HasMany
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function transferItems(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(Adjustment::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // ── Stock Calculation (NEVER stored on product) ────────

    /**
     * Get current stock for this product at a specific location.
     * Calculated as SUM(quantity_change) from the stock_ledger.
     */
    public function getStockAtLocation($locationId): int
    {
        return (int) $this->stockLedgerEntries()
            ->where('location_id', $locationId)
            ->sum('quantity_change');
    }

    /**
     * Accessor: total stock across ALL locations.
     * Usage: $product->total_stock
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->stockLedgerEntries()
            ->sum('quantity_change');
    }

    /**
     * Profit margin percentage: ((selling - cost) / cost) * 100
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->unit_cost == 0) {
            return 0;
        }
        return round((($this->selling_price - $this->unit_cost) / $this->unit_cost) * 100, 2);
    }
}
