<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DailyAccountOpening;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Fills the remaining domain and reference tables that the other seeders do not
 * touch:
 *
 *  - daily_account_openings : daily account opening counts per branch
 *  - user_branch            : user -> branch scoping (pivot)
 *  - user_group_user        : user -> group pivot, mirroring users.group_id
 *  - login_histories        : sample sign-in audit trail
 *  - user_audits            : sample audit trail
 *
 * Every writer is idempotent (updateOrCreate or insertOrIgnore style), so the
 * seeder can be re-run without duplicating rows.
 */
class ReferenceDataSeeder extends Seeder
{
    private const DAYS = 45;

    public function run(): void
    {
        $branches = Branch::query()->orderBy('id')->get();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Run TestDataSeeder first.');

            return;
        }

        $lastDay = Carbon::yesterday();
        $firstDay = $lastDay->copy()->subDays(self::DAYS - 1);

        $this->command->info('daily_account_openings: ' . $this->seedAccountOpenings($branches, $firstDay, $lastDay) . ' rows.');
        $this->command->info('user_branch: ' . $this->seedUserBranchScopes() . ' assignments.');
        $this->command->info('user_group_user: ' . $this->seedUserGroupPivot() . ' assignments.');
        $this->command->info('login_histories: ' . $this->seedLoginHistories() . ' rows.');
        $this->command->info('user_audits: ' . $this->seedUserAudits($branches) . ' rows.');
    }

    /**
     * Daily account openings per branch. A branch reports only its own banking
     * type: Conventional branches fill conventional_accounts, IFB branches fill
     * ifb_accounts, and the other column stays at zero.
     */
    protected function seedAccountOpenings($branches, Carbon $firstDay, Carbon $lastDay): int
    {
        $written = 0;

        DB::transaction(function () use ($branches, $firstDay, $lastDay, &$written): void {
            foreach ($branches as $branch) {
                $seed = (int) $branch->id * 7919;
                $isIfb = (int) $branch->bankingType_id === 2;

                $dailyBase = $isIfb ? 4 : 9;
                $target = $dailyBase + (($seed / 100) % 6);

                for ($i = 0; $i < self::DAYS; $i++) {
                    $day = $firstDay->copy()->addDays($i);
                    $weekday = (int) $day->dayOfWeek;
                    $weekend = ($weekday === 0 || $weekday === 6) ? 0.25 : 1.0;
                    $noise = 1 + (((($i * 17) + $seed) % 60) / 100);

                    $openings = (int) max(0, round($target * $weekend * $noise));

                    DailyAccountOpening::updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'business_day' => $day->toDateString(),
                        ],
                        [
                            // A Conventional branch reports both banking types.
                            // An IFB branch has no Conventional business, so that
                            // column is null rather than 0 - "not applicable" must
                            // not read as "reported zero".
                            'conventional_accounts' => $isIfb ? null : $openings,
                            'ifb_accounts' => $openings,
                            'target_accounts' => $target,
                            'remarks' => null,
                        ]
                    );

                    $written++;
                }
            }
        });

        return $written;
    }

    /**
     * Gives each user a slice of branches so branch scoping is demonstrable.
     * The first branch in a user's slice is flagged as their primary branch.
     */
    protected function seedUserBranchScopes(): int
    {
        $branches = Branch::query()->orderBy('id')->get();
        $users = User::query()->orderBy('id')->get();

        $written = 0;

        DB::transaction(function () use ($users, $branches, &$written): void {
            foreach ($users as $index => $user) {
                $slice = $branches->slice($index * 3, 5)->values();

                if ($slice->isEmpty()) {
                    $slice = $branches->take(3)->values();
                }

                foreach ($slice as $position => $branch) {
                    $exists = DB::table('user_branch')
                        ->where('user_id', $user->id)
                        ->where('branch_id', $branch->id)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    DB::table('user_branch')->insert([
                        'user_id' => $user->id,
                        'branch_id' => $branch->id,
                        'is_primary' => $position === 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $written++;
                }
            }
        });

        return $written;
    }

    /**
     * Mirrors the denormalised users.group_id into the user_group_user pivot so
     * both representations agree.
     */
    protected function seedUserGroupPivot(): int
    {
        $users = User::query()->whereNotNull('group_id')->orderBy('id')->get();

        $written = 0;

        DB::transaction(function () use ($users, &$written): void {
            foreach ($users as $user) {
                $exists = DB::table('user_group_user')
                    ->where('user_id', $user->id)
                    ->where('group_id', $user->group_id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('user_group_user')->insert([
                    'user_id' => $user->id,
                    'group_id' => $user->group_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $written++;
            }
        });

        return $written;
    }

    /**
     * Three sign-ins per user, staggered over the past week, plus one deliberate
     * failure so the login history screen has something to filter on.
     */
    protected function seedLoginHistories(): int
    {
        $users = User::query()->orderBy('id')->get();
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131.0.0.0 Safari/537.36';

        $written = 0;

        DB::transaction(function () use ($users, $agent, &$written): void {
            foreach ($users as $index => $user) {
                for ($n = 0; $n < 3; $n++) {
                    DB::table('login_histories')->insert([
                        'user_id' => $user->id,
                        'ip_address' => '127.0.0.1',
                        'user_agent' => $agent,
                        'status' => 'success',
                        'reason' => null,
                        'logged_in_at' => now()->subDays($index + $n)->subHours($n * 3),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $written++;
                }

                // One failed attempt, so the "failed" filter is not empty.
                DB::table('login_histories')->insert([
                    'user_id' => $user->id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => $agent,
                    'status' => 'failed',
                    'reason' => 'Invalid credentials',
                    'logged_in_at' => now()->subDays($index)->subHours(6),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $written++;
            }
        });

        return $written;
    }

    /**
     * A short audit trail per user across the three most useful actions.
     */
    protected function seedUserAudits($branches): int
    {
        $users = User::query()->orderBy('id')->get();
        $count = $branches->count();

        $actions = ['created', 'updated', 'viewed'];

        $written = 0;

        DB::transaction(function () use ($users, $branches, $count, $actions, &$written): void {
            foreach ($users as $index => $user) {
                foreach ($actions as $n => $action) {
                    $branch = $branches[($index + $n) % $count];

                    $oldValues = null;
                    $newValues = ['name' => $branch->name];

                    if ($action === 'updated') {
                        $oldValues = ['name' => $branch->name . ' (old)'];
                    } elseif ($action === 'created') {
                        $newValues = $newValues + [
                            'bankingType_id' => $branch->bankingType_id,
                            'district_id' => $branch->district_id,
                        ];
                    }

                    DB::table('user_audits')->insert([
                        'user_id' => $user->id,
                        'performed_by' => $user->id,
                        'action' => $action,
                        'model_type' => Branch::class,
                        'model_id' => $branch->id,
                        'old_values' => $oldValues ? json_encode($oldValues) : null,
                        'new_values' => json_encode($newValues),
                        'ip_address' => '127.0.0.1',
                        'remarks' => ucfirst($action) . ' ' . $branch->name,
                        'created_at' => now()->subDays($n),
                        'updated_at' => now()->subDays($n),
                    ]);

                    $written++;
                }
            }
        });

        return $written;
    }
}
