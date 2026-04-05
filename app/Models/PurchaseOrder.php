<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference_no', 'vendor_id', 'status', 'expected_date'])
            ->logOnlyDirty()
            ->useLogName('purchase_order');
    }

    public $timestamps = true;

    protected $fillable = [
        'reference_no',
        'vendor_id',
        'status',
        'expected_date',
        'notes',
        'receipt_id',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    // ── Accessors ──────────────────────────────────────────

    /**
     * Sum of (quantity × unit_cost) for all line items.
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_cost;
        });
    }

    /**
     * Total number of units across all line items.
     */
    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
