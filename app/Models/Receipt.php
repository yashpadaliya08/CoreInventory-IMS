<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Receipt extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference_no', 'vendor_name', 'vendor_id', 'status'])
            ->logOnlyDirty()
            ->useLogName('receipt');
    }

    public $timestamps = true;

    protected $fillable = [
        'reference_no',
        'vendor_name',
        'vendor_id',
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

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }
}
