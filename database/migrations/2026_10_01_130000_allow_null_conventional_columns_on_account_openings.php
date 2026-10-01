<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A Conventional branch reports openings for BOTH banking types, so both columns
 * are always populated for it. An IFB branch has no Conventional business, so its
 * conventional_accounts is not applicable and must be stored as NULL.
 *
 * conventional_accounts was NOT NULL DEFAULT 0, which made "not applicable"
 * indistinguishable from "reported zero": every IFB branch appeared to have zero
 * Conventional openings. Nullable storage keeps those two cases distinct.
 *
 * ifb_accounts stays NOT NULL because it is always reported: Conventional
 * branches fill it too.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_account_openings', function (Blueprint $table) {
            $table->unsignedInteger('conventional_accounts')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        // Backfill NULLs first, otherwise NOT NULL cannot be restored.
        DB::table('daily_account_openings')
            ->whereNull('conventional_accounts')
            ->update(['conventional_accounts' => 0]);

        Schema::table('daily_account_openings', function (Blueprint $table) {
            $table->unsignedInteger('conventional_accounts')->nullable(false)->default(0)->change();
        });
    }
};
