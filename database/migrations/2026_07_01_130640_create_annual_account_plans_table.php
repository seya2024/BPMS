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
     Schema::create('annual_account_plans', function (Blueprint $table) {
    $table->id();

    $table->foreignId('annual_plan_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->unsignedInteger('annual_target_accounts')->default(0);

    $table->unsignedInteger('q1_target_accounts')->default(0);
    $table->unsignedInteger('q2_target_accounts')->default(0);
    $table->unsignedInteger('q3_target_accounts')->default(0);
    $table->unsignedInteger('q4_target_accounts')->default(0);

    $table->unsignedInteger('monthly_target_accounts')->default(0);
    $table->unsignedInteger('weekly_target_accounts')->default(0);
    $table->unsignedInteger('daily_target_accounts')->default(0);

    $table->text('remarks')->nullable();

    $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_account_plans');
    }
};
