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
            // Layout customization stored as JSON
            $table->longText('layout_settings')->nullable()->after('is_published');
            // Example: {
            //   "kop_position": "top",
            //   "kop_alignment": "center",
            //   "kop_height": 100,
            //   "kop_spacing": 20,
            //   "header_alignment": "center",
            //   "header_height": 70,
            //   "header_font_size": "14px",
            //   "body_alignment": "left",
            //   "body_height": 200,
            //   "body_line_height": "1.6",
            //   "footer_layout": "2col",
            //   "footer_height": 150,
            //   "footer_alignment": "center",
            //   "document_margin": 20,
            //   "document_font_family": "Times New Roman"
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('layout_settings');
        });
    }
};
