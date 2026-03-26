<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'payable_type',
        'payable_id',
        'transaction_id',
        'amount',
        'payment_method',
        'status',
        'payment_details',
        'insurance_coverage',
        'patient_payment',
        'notes',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'amount' => 'decimal:2',
        'insurance_coverage' => 'decimal:2',
        'patient_payment' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the payable model
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Get the insurance claim
     */
    public function insuranceClaim(): HasOne
    {
        return $this->hasOne(InsuranceClaim::class);
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
            ->whereNotNull('paid_at');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
