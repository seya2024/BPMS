<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_account_openings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->date('business_day');
            $table->integer('conventional_accounts')->default(0);
            $table->integer('ifb_accounts')->default(0);
            $table->integer('target_accounts')->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['branch_id', 'business_day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_account_openings');
    }
};
