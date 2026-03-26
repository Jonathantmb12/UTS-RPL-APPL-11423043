<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorPerformanceMetric extends Model
{
    use HasFactory;

    protected $table = 'doctor_performance_metrics';

    protected $fillable = [
        'doctor_id',
        'total_appointments',
        'completed_appointments',
        'cancelled_appointments',
        'average_rating',
        'patient_count',
        'specialization_stats',
        'response_time_hours',
        'monthly_stats',
        'last_updated',
    ];

    protected $casts = [
        'specialization_stats' => 'array',
        'monthly_stats' => 'array',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the doctor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
