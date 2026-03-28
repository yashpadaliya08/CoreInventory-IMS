<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'reference_no',
        'vendor_name',
        'status',
        'expected_date',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function receiptItems(): HasMany
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function stockLedgerEntries(): HasMany
    {
        return $this->hasMany(StockLedger::class, 'reference_id')
            ->where('reference_type', self::class);
    }
}
