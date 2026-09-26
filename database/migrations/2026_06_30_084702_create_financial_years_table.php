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

    // Only one record should be OPEN.
    //table->foreignId('financial_year_id')->constrained();
    //$currentYear = FinancialYear::where('status', 'OPEN')->firstOrFail();

    Schema::create('financial_years', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // FY2026/27
    $table->date('start_date')->nullable();  //2026-07-01
    $table->date('end_date')->nullable();  // 2027-06-30
    $table->enum('status', [ 'OPEN','CLOSING','CLOSED'])->default('OPEN');  // OPEN
    $table->timestamp('opened_at')->nullable();
    $table->timestamp('closed_at')->nullable();
    $table->foreignId('closed_by')->nullable()->constrained('users') ->nullOnDelete();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_years');
    }
};

