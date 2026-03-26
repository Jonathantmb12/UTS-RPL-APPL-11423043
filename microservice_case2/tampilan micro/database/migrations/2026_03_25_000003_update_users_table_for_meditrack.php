<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column
            $table->enum('role', ['admin', 'doctor', 'patient', 'pharmacist'])->default('patient')->after('email');
            
            // Doctor specific fields
            $table->string('specialization')->nullable()->after('role');
            $table->string('license_number')->nullable()->unique()->after('specialization');
            $table->string('hospital_name')->nullable()->after('license_number');
            
            // Patient specific fields
            $table->date('date_of_birth')->nullable()->after('hospital_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('phone_number')->nullable()->after('gender');
            $table->text('address')->nullable()->after('phone_number');
            $table->string('emergency_contact')->nullable()->after('address');
            $table->string('blood_type')->nullable()->after('emergency_contact');
            $table->text('allergies')->nullable()->after('blood_type');
            
            // Pharmacist specific fields
            $table->string('pharmacy_name')->nullable()->after('allergies');
            $table->string('pharmacy_license')->nullable()->unique()->after('pharmacy_name');
            $table->text('pharmacy_address')->nullable()->after('pharmacy_license');
            
            // Common fields
            $table->string('profile_picture')->nullable()->after('pharmacy_address');
            $table->boolean('is_verified')->default(false)->after('profile_picture');
            $table->string('verification_token')->nullable()->unique()->after('is_verified');
            $table->timestamp('verified_at')->nullable()->after('verification_token');
            $table->timestamp('last_login_at')->nullable()->after('verified_at');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'specialization', 'license_number', 'hospital_name',
                'date_of_birth', 'gender', 'phone_number', 'address', 'emergency_contact',
                'blood_type', 'allergies', 'pharmacy_name', 'pharmacy_license', 'pharmacy_address',
                'profile_picture', 'is_verified', 'verification_token', 'verified_at', 'last_login_at',
                'is_active'
            ]);
            $table->dropSoftDeletes();
        });
    }
};
