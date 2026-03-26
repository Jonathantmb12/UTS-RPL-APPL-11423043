<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lab_results';

    protected $fillable = [
        'patient_id',
        'ordered_by_doctor_id',
        'test_name',
        'description',
        'status',
        'test_parameters',
        'results',
        'clinical_notes',
        'test_file',
        'ordered_date',
        'completed_date',
    ];

    protected $casts = [
        'test_parameters' => 'array',
        'results' => 'array',
        'ordered_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    /**
     * Get the patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the doctor who ordered the test
     */
    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_by_doctor_id');
    }

    /**
     * Scope for completed tests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending tests
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['ordered', 'in-progress']);
    }
}
