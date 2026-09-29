<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->unsignedSmallInteger('pos_number_x')->default(200);
            $table->unsignedSmallInteger('pos_number_y')->default(40);
            $table->unsignedSmallInteger('pos_name_x')->default(250);
            $table->unsignedSmallInteger('pos_name_y')->default(220);
            $table->unsignedSmallInteger('pos_qr_x')->default(680);
            $table->unsignedSmallInteger('pos_qr_y')->default(440);
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn([
                'pos_number_x',
                'pos_number_y',
                'pos_name_x',
                'pos_name_y',
                'pos_qr_x',
                'pos_qr_y',
            ]);
        });
    }
};
