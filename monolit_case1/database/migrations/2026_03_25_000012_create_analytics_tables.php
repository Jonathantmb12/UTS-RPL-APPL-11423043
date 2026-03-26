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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_type'); // 'patient_outcomes', 'doctor_performance', 'drug_usage'
            $table->morphs('entity'); // related entity (doctor, patient, medication)
            $table->json('data'); // metric data
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->timestamps();

            $table->index('metric_type');
            $table->index('period_start');
        });

        Schema::create('patient_outcomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->text('outcome_summary');
            $table->enum('recovery_status', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->integer('follow_up_appointments')->default(0);
            $table->float('satisfaction_score')->nullable(); // 1-5
            $table->json('symptoms_progression')->nullable();
            $table->dateTime('recorded_date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users');

            $table->index('patient_id');
            $table->index('doctor_id');
        });

        Schema::create('doctor_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_appointments')->default(0);
            $table->integer('completed_appointments')->default(0);
            $table->integer('cancelled_appointments')->default(0);
            $table->float('average_rating')->default(0);
            $table->integer('patient_count')->default(0);
            $table->json('specialization_stats')->nullable();
            $table->float('response_time_hours')->nullable();
            $table->json('monthly_stats')->nullable();
            $table->dateTime('last_updated');
            $table->timestamps();

            $table->index('doctor_id');
        });

        Schema::create('drug_usage_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('medication_name');
            $table->integer('total_prescribed')->default(0);
            $table->integer('total_dispensed')->default(0);
            $table->integer('active_prescriptions')->default(0);
            $table->json('doctor_usage')->nullable(); // top doctors prescribing
            $table->json('patient_demographics')->nullable(); // age group, gender
            $table->json('side_effects_reported')->nullable();
            $table->float('effectiveness_rating')->nullable();
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->timestamps();

            $table->index('medication_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
        Schema::dropIfExists('patient_outcomes');
        Schema::dropIfExists('doctor_performance_metrics');
        Schema::dropIfExists('drug_usage_analytics');
    }
};
