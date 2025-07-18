<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modul', function (Blueprint $table) {
            if (!Schema::hasColumn('modul', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('poin_reward');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modul', function (Blueprint $table) {
            if (Schema::hasColumn('modul', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
