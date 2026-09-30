<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Account status
            $table->string('status')->default('active')->after('group_id'); // active, inactive, pending, locked
            $table->boolean('is_active')->default(true)->after('status');
            $table->boolean('is_locked')->default(false)->after('is_active');
            $table->boolean('must_change_password')->default(false)->after('is_locked');
            $table->timestamp('last_login_at')->nullable()->after('must_change_password');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_at')->nullable()->after('failed_login_attempts');
            $table->timestamp('password_changed_at')->nullable()->after('locked_at');
            $table->timestamp('email_verified_at')->nullable()->change();

            // Organizational assignment
            $table->unsignedBigInteger('branch_id')->nullable()->after('group_id');
            $table->unsignedBigInteger('district_id')->nullable()->after('branch_id');
            $table->string('department')->nullable()->after('district_id');
            $table->string('job_title')->nullable()->after('department');
            $table->string('phone')->nullable()->after('job_title');

            // MFA
            $table->boolean('mfa_enabled')->default(false)->after('phone');
            $table->string('mfa_secret')->nullable()->after('mfa_enabled');

            // Approval
            $table->unsignedBigInteger('approved_by')->nullable()->after('mfa_secret');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Soft delete
            $table->softDeletes();

            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        // Create user-group pivot table (many-to-many)
        Schema::create('user_group_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('group_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('group_id')->references('id')->on('user_groups')->cascadeOnDelete();
            $table->unique(['user_id', 'group_id']);
        });

        // Create user_branch pivot table (many-to-many for branch assignments)
        Schema::create('user_branch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->unique(['user_id', 'branch_id']);
        });

        // Create login history table
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('status'); // success, failed, locked
            $table->string('reason')->nullable();
            $table->timestamp('logged_in_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // Create user audit log table
        Schema::create('user_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->string('action'); // created, updated, deleted, login, logout, password_reset, etc.
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_audits');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('user_branch');
        Schema::dropIfExists('user_group_user');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status', 'is_active', 'is_locked', 'must_change_password',
                'last_login_at', 'last_login_ip', 'failed_login_attempts',
                'locked_at', 'password_changed_at', 'branch_id', 'district_id',
                'department', 'job_title', 'phone', 'mfa_enabled', 'mfa_secret',
                'approved_by', 'approved_at', 'deleted_at',
            ]);
        });
    }
};
