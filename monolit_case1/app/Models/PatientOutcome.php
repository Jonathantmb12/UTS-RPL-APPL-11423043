<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientOutcome extends Model
{
    use HasFactory;

    protected $table = 'patient_outcomes';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'outcome_summary',
        'recovery_status',
        'follow_up_appointments',
        'satisfaction_score',
        'symptoms_progression',
        'recorded_date',
    ];

    protected $casts = [
        'symptoms_progression' => 'array',
        'recorded_date' => 'datetime',
    ];

    /**
     * Get the patient
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the doctor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
