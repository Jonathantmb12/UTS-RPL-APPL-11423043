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
        Schema::create('pharmacy_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('users')->onDelete('cascade');
            $table->string('medication_name');
            $table->string('generic_name')->nullable();
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->integer('reorder_quantity')->default(50);
            $table->decimal('unit_price', 10, 2);
            $table->string('batch_number')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('pharmacy_id');
            $table->index('sku');
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_inventory');
    }
};
