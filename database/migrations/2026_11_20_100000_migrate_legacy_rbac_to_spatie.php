<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['roles', 'permissions'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                throw new RuntimeException("The legacy {$tableName} table is required for RBAC migration.");
            }

            if (! Schema::hasColumn($tableName, 'guard_name')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('guard_name')->default('web');
                });
            }

            $singleColumnIndex = collect(Schema::getIndexes($tableName))->first(
                fn (array $index): bool => $index['unique'] && $index['columns'] === ['name']
            );

            if ($singleColumnIndex) {
                Schema::table($tableName, function (Blueprint $table) use ($singleColumnIndex) {
                    $table->dropUnique($singleColumnIndex['name']);
                });
            }

            $guardScopedIndexExists = collect(Schema::getIndexes($tableName))->contains(
                fn (array $index): bool => $index['unique'] && $index['columns'] === ['name', 'guard_name']
            );

            if (! $guardScopedIndexExists) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unique(['name', 'guard_name']);
                });
            }
        }

        $this->createPermissionTables();
        $this->copyLegacyRoleAssignments();
        $this->copyLegacyPermissionAssignments();

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
    }

    public function down(): void
    {
        if (DB::table('model_has_permissions')->exists()) {
            throw new RuntimeException('Direct model permissions cannot be represented by the legacy schema.');
        }

        $duplicateRoleNames = DB::table('roles')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->exists();
        $duplicatePermissionNames = DB::table('permissions')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicateRoleNames || $duplicatePermissionNames) {
            throw new RuntimeException('Duplicate names across guards prevent a safe rollback.');
        }

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        $userRoleAssignments = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->get(['role_id', 'model_id']);

        foreach ($userRoleAssignments->chunk(500) as $assignments) {
            DB::table('role_user')->insertOrIgnore(
                $assignments->map(fn (object $assignment): array => [
                    'role_id' => $assignment->role_id,
                    'user_id' => $assignment->model_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all()
            );
        }

        $rolePermissionAssignments = DB::table('role_has_permissions')
            ->get(['permission_id', 'role_id']);

        foreach ($rolePermissionAssignments->chunk(500) as $assignments) {
            DB::table('permission_role')->insertOrIgnore(
                $assignments->map(fn (object $assignment): array => [
                    'permission_id' => $assignment->permission_id,
                    'role_id' => $assignment->role_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all()
            );
        }

        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');

        foreach (['roles', 'permissions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropUnique("{$tableName}_name_guard_name_unique");
                $table->dropColumn('guard_name');
                $table->unique('name');
            });
        }
    }

    private function createPermissionTables(): void
    {
        if (! Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type']);
                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
                $table->primary(
                    ['permission_id', 'model_id', 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            });
        }

        if (! Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type']);
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->primary(
                    ['role_id', 'model_id', 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            });
        }

        if (! Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->primary(
                    ['permission_id', 'role_id'],
                    'role_has_permissions_permission_id_role_id_primary'
                );
            });
        }
    }

    private function copyLegacyRoleAssignments(): void
    {
        if (! Schema::hasTable('role_user')) {
            return;
        }

        $assignments = DB::table('role_user')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->get(['role_user.role_id', 'role_user.user_id']);

        foreach ($assignments->chunk(500) as $chunk) {
            DB::table('model_has_roles')->insertOrIgnore(
                $chunk->map(fn (object $assignment): array => [
                    'role_id' => $assignment->role_id,
                    'model_type' => User::class,
                    'model_id' => $assignment->user_id,
                ])->all()
            );
        }
    }

    private function copyLegacyPermissionAssignments(): void
    {
        if (! Schema::hasTable('permission_role')) {
            return;
        }

        $assignments = DB::table('permission_role')
            ->join('roles', 'roles.id', '=', 'permission_role.role_id')
            ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
            ->get(['permission_role.permission_id', 'permission_role.role_id']);

        foreach ($assignments->chunk(500) as $chunk) {
            DB::table('role_has_permissions')->insertOrIgnore(
                $chunk->map(fn (object $assignment): array => [
                    'permission_id' => $assignment->permission_id,
                    'role_id' => $assignment->role_id,
                ])->all()
            );
        }
    }
};
