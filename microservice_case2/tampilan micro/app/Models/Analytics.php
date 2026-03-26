<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_type',
        'entity_type',
        'entity_id',
        'data',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'data' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    /**
     * Get the entity
     */
    public function entity()
    {
        return $this->morphTo();
    }
}
