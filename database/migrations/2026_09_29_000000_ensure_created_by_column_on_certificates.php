<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('certificates') && ! Schema::hasColumn('certificates', 'created_by')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->string('created_by')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('certificates') && Schema::hasColumn('certificates', 'created_by')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }
};
