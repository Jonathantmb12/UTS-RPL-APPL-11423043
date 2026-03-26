<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class PharmacyInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pharmacy_inventory';

    protected $fillable = [
        'pharmacy_id',
        'medication_name',
        'generic_name',
        'sku',
        'stock_quantity',
        'reorder_level',
        'reorder_quantity',
        'unit_price',
        'batch_number',
        'expiration_date',
        'manufacturer',
        'description',
        'is_active',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the pharmacy
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacy_id');
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'reorder_level');
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for expired items
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<=', now());
    }
}
