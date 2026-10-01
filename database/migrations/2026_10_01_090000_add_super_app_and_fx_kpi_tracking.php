<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds tracking for the two KPIs that previously had no measurement source:
 *   - Super App Subscriptions
 *   - Foreign Currency Generation
 *
 * Also repairs three naming defects:
 *   - annual_plans.supperappsubscription -> super_app_subscriptions (double "p" typo)
 *   - KPI " Super App Subscriptin"       -> "Super App Subscriptions" (leading space, truncated)
 *   - KPI "Forign Currency"              -> "Foreign Currency Generation" (spelling)
 *
 * All changes are additive or renames. No rows are deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Repair the misspelled target column (rename preserves the data).
        if (Schema::hasColumn('annual_plans', 'supperappsubscription')) {
            Schema::table('annual_plans', function (Blueprint $table): void {
                $table->renameColumn('supperappsubscription', 'super_app_subscriptions');
            });
        }

        // 2. Foreign currency needs a district-level target alongside the existing ones.
        if (! Schema::hasColumn('annual_plans', 'foreign_currency_target')) {
            Schema::table('annual_plans', function (Blueprint $table): void {
                $table->decimal('foreign_currency_target', 18, 2)->default(0)->after('account');
            });
        }

        // 3. Daily super app subscription tracking.
        if (! Schema::hasTable('daily_super_app_subscriptions')) {
            Schema::create('daily_super_app_subscriptions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->date('business_day');
                $table->unsignedInteger('subscriptions')->default(0);
                $table->unsignedInteger('target_subscriptions')->default(0);
                $table->text('remarks')->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['branch_id', 'business_day'], 'super_app_sub_branch_day_unique');
                $table->index('business_day');
            });
        }

        // 4. Daily foreign currency generation tracking.
        if (! Schema::hasTable('daily_foreign_currency_generations')) {
            Schema::create('daily_foreign_currency_generations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->date('business_day');
                $table->decimal('amount', 18, 2)->default(0);
                $table->decimal('target_amount', 18, 2)->default(0);
                $table->string('currency_code', 8)->default('ETB');
                $table->text('remarks')->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['branch_id', 'business_day'], 'fx_gen_branch_day_unique');
                $table->index('business_day');
            });
        }

        // 5. Repair the KPI definitions themselves.
        DB::table('k_p_i_s')->where('name', 'LIKE', '%Super App%')->update([
            'name' => 'Super App Subscriptions',
            'unit' => 'number',
            'calculation_method' => 'Cumulative active super app subscriptions vs district annual target',
            'category_id' => DB::table('k_p_i_categories')->where('name', 'Digital Banking')->value('id'),
            'updated_at' => now(),
        ]);

        DB::table('k_p_i_s')->where('name', 'LIKE', '%Forign Currency%')
            ->orWhere('name', 'LIKE', '%Foreign Currency%')
            ->update([
                'name' => 'Foreign Currency Generation',
                'unit' => 'amount',
                'calculation_method' => 'Cumulative foreign currency generated vs district annual target',
                'category_id' => DB::table('k_p_i_categories')->where('name', 'Deposit Mobilization')->value('id'),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_foreign_currency_generations');
        Schema::dropIfExists('daily_super_app_subscriptions');

        if (Schema::hasColumn('annual_plans', 'foreign_currency_target')) {
            Schema::table('annual_plans', function (Blueprint $table): void {
                $table->dropColumn('foreign_currency_target');
            });
        }

        if (Schema::hasColumn('annual_plans', 'super_app_subscriptions')) {
            Schema::table('annual_plans', function (Blueprint $table): void {
                $table->renameColumn('super_app_subscriptions', 'supperappsubscription');
            });
        }
    }
};
