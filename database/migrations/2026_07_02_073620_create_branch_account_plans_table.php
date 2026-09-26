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
        Schema::create('branch_account_plans', function (Blueprint $table) {
              $table->id();

            $table->foreignId('annual_account_plan_id')
                ->constrained('annual_account_plans')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->unsignedInteger('annual_target_accounts')->nullable();

            $table->unsignedInteger('q1_target_accounts')->nullable();
            $table->unsignedInteger('q2_target_accounts')->nullable();
            $table->unsignedInteger('q3_target_accounts')->nullable();
            $table->unsignedInteger('q4_target_accounts')->nullable();

            $table->unsignedInteger('monthly_target_accounts')->nullable();
            $table->unsignedInteger('weekly_target_accounts')->nullable();
            $table->unsignedInteger('daily_target_accounts')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            // Prevent duplicate allocation for the same branch in the same annual plan
            $table->unique(['annual_account_plan_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_account_plans');
    }
};
