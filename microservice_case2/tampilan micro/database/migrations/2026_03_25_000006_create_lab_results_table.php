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
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('ordered_by_doctor_id')->nullable();
            $table->string('test_name');
            $table->text('description')->nullable();
            $table->enum('status', ['ordered', 'in-progress', 'completed', 'cancelled'])->default('ordered');
            $table->json('test_parameters')->nullable(); // Store multiple test params
            $table->json('results')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->string('test_file')->nullable(); // PDF/image upload
            $table->dateTime('ordered_date');
            $table->dateTime('completed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ordered_by_doctor_id')->references('id')->on('users');

            $table->index('patient_id');
            $table->index('ordered_by_doctor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
