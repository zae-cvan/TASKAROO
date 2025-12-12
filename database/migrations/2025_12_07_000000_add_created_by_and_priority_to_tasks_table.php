<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'created_by_id')) {
                $table->foreignId('created_by_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->integer('priority')->default(0)->after('is_pinned');
            }
        });

        // Backfill created_by_id with existing user_id, and ensure priority is set
        if (Schema::hasTable('tasks')) {
            DB::table('tasks')->whereNull('created_by_id')->update(['created_by_id' => DB::raw('user_id')]);
            DB::table('tasks')->whereNull('priority')->update(['priority' => 0]);
        }
    }

    public function down(): void {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'created_by_id')) {
                $table->dropForeign(['created_by_id']);
                $table->dropColumn('created_by_id');
            }
            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};
