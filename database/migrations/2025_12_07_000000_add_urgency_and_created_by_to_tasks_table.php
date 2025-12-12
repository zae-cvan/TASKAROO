<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tasks', function (Blueprint $table) {
            // urgency: very_urgent, urgent, normal, least_urgent
            $table->enum('urgency', ['very_urgent','urgent','normal','least_urgent'])->default('normal')->after('category');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('urgency');
        });
    }

    public function down(): void {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by','urgency']);
        });
    }
};
