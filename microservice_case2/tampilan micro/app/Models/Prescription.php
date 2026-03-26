<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'medication_name',
        'description',
        'dosage',
        'frequency',
        'quantity',
        'duration_days',
        'instructions',
        'side_effects_warning',
        'status',
        'prescribed_date',
        'expiration_date',
    ];

    protected $casts = [
        'prescribed_date' => 'datetime',
        'expiration_date' => 'datetime',
    ];

    /**
     * Get the patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the doctor
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the appointment
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get prescription orders
     */
    public function prescriptionOrders(): HasMany
    {
        return $this->hasMany(PrescriptionOrder::class);
    }

    /**
     * Scope for active prescriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expiration_date', '>', now());
    }

    /**
     * Scope for expired prescriptions
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere('expiration_date', '<=', now());
        });
    }
}
