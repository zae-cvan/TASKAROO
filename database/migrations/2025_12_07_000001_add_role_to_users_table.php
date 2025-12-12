<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('email');
            }
        });

        // Optionally mark the first user as admin if none exist
        if (Schema::hasTable('users')) {
            $first = DB::table('users')->first();
            if ($first && DB::table('users')->where('role', 'admin')->count() === 0) {
                DB::table('users')->where('id', $first->id)->update(['role' => 'admin']);
            }
        }
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
