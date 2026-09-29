<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('created_by')->nullable()->after('id');
            $table->string('certificate_number')->unique();
            $table->string('recipient_name');
            $table->string('recipient_identity')->nullable();
            $table->string('institution')->nullable();
            $table->string('event_name');
            $table->string('role')->nullable();
            $table->date('issue_date');
            $table->string('template_path');
            $table->string('qr_token')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
