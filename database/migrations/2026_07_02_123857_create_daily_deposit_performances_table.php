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
 Schema::create('daily_deposit_performances', function (Blueprint $table) {
      
     $table->id();
    $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
    // Deposit position
    $table->decimal('total_deposit_amount', 20, 2)->default(0);
    // Daily movement
    $table->decimal('new_deposit_amount', 20, 2)->default(0);
    $table->decimal('deposit_inflow_amount', 20, 2)->default(0);
    $table->decimal('deposit_outflow_amount', 20, 2)->default(0);

    // Net daily change
    $table->decimal('net_deposit_change', 20, 2)->default(0);
     $table->date('business_day')->nullable();
    $table->text('remarks')->nullable();
    $table->timestamps();
    $table->unique(['branch_id', 'business_day']);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_deposit_performances');
    }
};
