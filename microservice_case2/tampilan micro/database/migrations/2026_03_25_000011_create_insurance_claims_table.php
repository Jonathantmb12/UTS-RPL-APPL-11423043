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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('insurance_provider');
            $table->string('policy_number');
            $table->string('claim_number')->unique();
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected', 'partially_approved'])->default('submitted');
            $table->decimal('claim_amount', 10, 2);
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('submitted_date');
            $table->dateTime('decision_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('patient_id');
            $table->index('payment_id');
            $table->index('claim_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
