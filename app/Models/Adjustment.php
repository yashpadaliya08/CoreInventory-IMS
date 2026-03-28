<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adjustment extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'reference_no',
        'location_id',
        'product_id',
        'recorded_quantity',
        'physical_quantity',
        'difference_quantity',
        'status',
    ];

    // ── Relationships ──────────────────────────────────────

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockLedgerEntries(): HasMany
    {
        return $this->hasMany(StockLedger::class, 'reference_id')
            ->where('reference_type', self::class);
    }
}
