<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('tasks', 'urgency')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('urgency')->nullable()->after('category');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tasks', 'urgency')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('urgency');
            });
        }
    }
};
