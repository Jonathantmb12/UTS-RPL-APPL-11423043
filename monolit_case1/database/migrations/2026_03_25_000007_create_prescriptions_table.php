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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->string('medication_name');
            $table->text('description')->nullable();
            $table->string('dosage'); // e.g., "500mg"
            $table->string('frequency'); // e.g., "3 times daily"
            $table->integer('quantity');
            $table->integer('duration_days');
            $table->text('instructions')->nullable();
            $table->text('side_effects_warning')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired', 'cancelled'])->default('active');
            $table->dateTime('prescribed_date');
            $table->dateTime('expiration_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments');

            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
