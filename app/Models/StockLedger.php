<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockLedger extends Model
{
    use HasFactory;

    protected $table = 'stock_ledger';

    protected $fillable = [
        'product_id',
        'location_id',
        'reference_type',
        'reference_id',
        'quantity_change',
    ];

    // ── Relationships ──────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Polymorphic relationship to the source document
     * (Receipt, Delivery, Transfer, or Adjustment).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }
}
