<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrescriptionOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prescription_orders';

    protected $fillable = [
        'prescription_id',
        'pharmacy_id',
        'patient_id',
        'status',
        'total_price',
        'ordered_date',
        'ready_date',
        'picked_up_date',
        'notes',
    ];

    protected $casts = [
        'ordered_date' => 'datetime',
        'ready_date' => 'datetime',
        'picked_up_date' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the prescription
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Get the pharmacy
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacy_id');
    }

    /**
     * Get the patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the payment
     */
    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    /**
     * Scope for ready orders
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }
}
