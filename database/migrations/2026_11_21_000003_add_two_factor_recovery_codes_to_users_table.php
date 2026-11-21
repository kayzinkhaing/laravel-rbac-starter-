<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'two_factor_recovery_codes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('two_factor_recovery_codes')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Keep recovery codes so rolling back cannot lock users out of their accounts.
    }
};
