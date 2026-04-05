<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'contact_person', 'address'])
            ->logOnlyDirty()
            ->useLogName('vendor');
    }

    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
    ];

    // ── Relationships ──────────────────────────────────────

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    // ── Accessors ──────────────────────────────────────────

    /**
     * Total value of all approved POs for this vendor.
     */
    public function getTotalPurchaseValueAttribute(): float
    {
        return (float) $this->purchaseOrders()
            ->where('status', 'Approved')
            ->with('items')
            ->get()
            ->sum(function ($po) {
                return $po->items->sum(function ($item) {
                    return $item->quantity * $item->unit_cost;
                });
            });
    }
}
