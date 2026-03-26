<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceClaim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'insurance_claims';

    protected $fillable = [
        'patient_id',
        'payment_id',
        'insurance_provider',
        'policy_number',
        'claim_number',
        'status',
        'claim_amount',
        'approved_amount',
        'rejection_reason',
        'notes',
        'submitted_date',
        'decision_date',
    ];

    protected $casts = [
        'submitted_date' => 'datetime',
        'decision_date' => 'datetime',
        'claim_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
    ];

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
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope for approved claims
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending claims
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review']);
    }
}
