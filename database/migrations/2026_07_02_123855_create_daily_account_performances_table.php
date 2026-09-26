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
  Schema::create('daily_account_performances', function (Blueprint $table) {
       
    $table->id();
    $table->foreignId('branch_id')->constrained() ->cascadeOnDelete();
    $table->unsignedInteger('total_accounts')->default(0);
    $table->unsignedInteger('active_accounts')->default(0);
    $table->unsignedInteger('dormant_accounts')->default(0);
    $table->unsignedInteger('reactivated_accounts')->default(0);
    $table->unsignedInteger('new_accounts')->default(0);
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
        Schema::dropIfExists('daily_account_performances');
    }
};
