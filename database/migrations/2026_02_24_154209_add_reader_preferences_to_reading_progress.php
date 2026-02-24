<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_progress', function (Blueprint $table) {
            $table->string('view_mode', 10)->default('single')->after('is_completed');
            $table->decimal('pdf_scale', 3, 1)->default(1.0)->after('view_mode');
        });
    }

    public function down(): void
    {
        Schema::table('reading_progress', function (Blueprint $table) {
            $table->dropColumn(['view_mode', 'pdf_scale']);
        });
    }
};
