<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugUsageAnalytic extends Model
{
    use HasFactory;

    protected $table = 'drug_usage_analytics';

    protected $fillable = [
        'medication_name',
        'total_prescribed',
        'total_dispensed',
        'active_prescriptions',
        'doctor_usage',
        'patient_demographics',
        'side_effects_reported',
        'effectiveness_rating',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'doctor_usage' => 'array',
        'patient_demographics' => 'array',
        'side_effects_reported' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}
