<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gives each KPI a real measurement unit.
 *
 * The seeded units were the generic tokens "count" and "amount", which do not
 * tell a reader what a figure is measured in (accounts? subscriptions? birr?).
 * The widget and the KPI resource now surface `unit` directly, so it has to
 * carry a real unit.
 *
 * Matched by name rather than id so the mapping survives reseeding.
 */
return new class extends Migration
{
    /** KPI name => measurement unit. */
    private const UNITS = [
        'Account Opening' => 'Accounts',
        'Deposit' => 'ETB',
        'Super App Subscriptions' => 'Subscriptions',
        'Foreign Currency Generation' => 'ETB',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('k_p_i_s') || ! Schema::hasColumn('k_p_i_s', 'unit')) {
            return;
        }

        foreach (self::UNITS as $name => $unit) {
            DB::table('k_p_i_s')
                ->where('name', $name)
                ->update(['unit' => $unit, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('k_p_i_s') || ! Schema::hasColumn('k_p_i_s', 'unit')) {
            return;
        }

        // Restore the original generic tokens.
        $original = [
            'Account Opening' => 'count',
            'Deposit' => 'amount',
            'Super App Subscriptions' => 'count',
            'Foreign Currency Generation' => 'amount',
        ];

        foreach ($original as $name => $unit) {
            DB::table('k_p_i_s')
                ->where('name', $name)
                ->update(['unit' => $unit, 'updated_at' => now()]);
        }
    }
};
