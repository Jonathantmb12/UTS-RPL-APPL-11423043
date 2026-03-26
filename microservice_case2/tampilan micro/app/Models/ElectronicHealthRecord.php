<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElectronicHealthRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'electronic_health_records';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medical_history',
        'current_medications',
        'allergies',
        'previous_surgeries',
        'family_history',
        'lifestyle_notes',
        'blood_type',
        'height_cm',
        'weight_kg',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'body_temperature_celsius',
        'other_vitals',
    ];

    protected $casts = [
        'other_vitals' => 'array',
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
}
