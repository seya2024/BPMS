<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_deposit_performance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->date('business_day');
            $table->foreignId('banking_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_segment_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'business_day', 'banking_type_id', 'business_segment_id'], 'daily_dep_perf_det_unique');
            $table->index(['branch_id', 'business_day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_deposit_performance_details');
    }
};