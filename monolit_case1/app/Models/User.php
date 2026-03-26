<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'specialization',
        'license_number',
        'hospital_name',
        'date_of_birth',
        'gender',
        'phone_number',
        'address',
        'emergency_contact',
        'blood_type',
        'allergies',
        'pharmacy_name',
        'pharmacy_license',
        'pharmacy_address',
        'profile_picture',
        'is_verified',
        'verification_token',
        'verified_at',
        'last_login_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ============ Doctor Relations ============

    /**
     * Get appointments for this doctor
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * Get electronic health records managed by this doctor
     */
    public function healthRecords(): HasMany
    {
        return $this->hasMany(ElectronicHealthRecord::class, 'doctor_id');
    }

    /**
     * Get lab results ordered by this doctor
     */
    public function labOrders(): HasMany
    {
        return $this->hasMany(LabResult::class, 'ordered_by_doctor_id');
    }

    /**
     * Get prescriptions issued by this doctor
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    /**
     * Get performance metrics for this doctor
     */
    public function performanceMetrics()
    {
        return $this->hasOne(DoctorPerformanceMetric::class, 'doctor_id');
    }

    // ============ Patient Relations ============

    /**
     * Get patient's appointments
     */
    public function patientAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Get patient's health record
     */
    public function healthRecord()
    {
        return $this->hasOne(ElectronicHealthRecord::class, 'patient_id');
    }

    /**
     * Get patient's lab results
     */
    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class, 'patient_id');
    }

    /**
     * Get patient's prescriptions
     */
    public function patientPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    /**
     * Get patient's prescription orders
     */
    public function prescriptionOrders(): HasMany
    {
        return $this->hasMany(PrescriptionOrder::class, 'patient_id');
    }

    /**
     * Get patient's payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'patient_id');
    }

    /**
     * Get patient's insurance claims
     */
    public function insuranceClaims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class, 'patient_id');
    }

    /**
     * Get patient outcomes
     */
    public function patientOutcomes(): HasMany
    {
        return $this->hasMany(PatientOutcome::class, 'patient_id');
    }

    // ============ Pharmacist Relations ============

    /**
     * Get pharmacy inventory managed by this pharmacist
     */
    public function pharmacyInventory(): HasMany
    {
        return $this->hasMany(PharmacyInventory::class, 'pharmacy_id');
    }

    /**
     * Get prescription orders for this pharmacy
     */
    public function orderedPrescriptions(): HasMany
    {
        return $this->hasMany(PrescriptionOrder::class, 'pharmacy_id');
    }

    // ============ Scope Methods ============

    /**
     * Scope for active users only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified users only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for doctors only
     */
    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    /**
     * Scope for patients only
     */
    public function scopePatients($query)
    {
        return $query->where('role', 'patient');
    }

    /**
     * Scope for pharmacists only
     */
    public function scopePharmacists($query)
    {
        return $query->where('role', 'pharmacist');
    }

    /**
     * Scope for admins only
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }
}
