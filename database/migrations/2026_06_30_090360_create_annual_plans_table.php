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
        Schema::create('annual_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id') ->constrained() ->cascadeOnDelete();
            $table->foreignId('district_id') ->constrained()->cascadeOnDelete();
            $table->decimal('deposit', 20, 2)->nullable();
            $table->unsignedInteger('account')->nullable();
            $table->unsignedInteger('supperappsubscription')->nullable();

            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            // Prevent duplicate plans for the same district and financial year

            $table->unique(['financial_year_id', 'district_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_plans');
    }
};
