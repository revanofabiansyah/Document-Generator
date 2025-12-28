<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('kop_left_image')->nullable()->after('is_published');
            $table->string('kop_right_image')->nullable()->after('kop_left_image');
            $table->string('signature_image')->nullable()->after('kop_right_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['kop_left_image', 'kop_right_image', 'signature_image']);
        });
    }
};
