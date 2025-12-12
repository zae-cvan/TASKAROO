<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
            if (!Schema::hasColumn('tasks', 'is_pinned')) {
                Schema::table('tasks', function (Blueprint $table) {
                    $table->boolean('is_pinned')->default(false);
                });
            }
    }

    public function down()
    {
        if (Schema::hasColumn('tasks', 'is_pinned')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('is_pinned');
            });
        }
    }
};
