<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // SoftDeletes requires timestamps — Product now tracks created_at/updated_at/deleted_at
    public $timestamps = true;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'unit_of_measure',
        'reorder_level',
    ];

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
}
