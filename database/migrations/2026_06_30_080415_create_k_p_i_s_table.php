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
        Schema::create('k_p_i_s', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Account Opening
            $table->foreignId('category_id') ->constrained('k_p_i_categories')->cascadeOnDelete();
            $table->string('unit'); 
            // %, count, amount, ratio
            $table->text('calculation_method')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('k_p_i_s');
    }
};
