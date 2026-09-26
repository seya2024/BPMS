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
     Schema::create('financial_periods', function (Blueprint $table) {
    // Every financial year has 4 quarters, each quarter has a start and end date.
    // Every trasnaction must be linked to a financial period, and a financial year.
    // Every tranasction must contain fiancial period Id
    $table->id();
    $table->foreignId('financial_year_id')->constrained('financial_years')->cascadeOnDelete();
    $table->tinyInteger('quarter'); // 1,2,3,4
    $table->string('label'); // Q1 FY2026/27
    $table->date('start_date');
    $table->date('end_date');
    $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
    $table->timestamp('closed_at')->nullable();
    $table->timestamps();
   // $table->unique(['financial_year_id', 'quarter']);
   });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_periods');
    }
};
