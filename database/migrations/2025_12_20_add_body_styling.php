<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Document;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add styling fields to existing documents' body parts
        $documents = Document::all();
        
        foreach ($documents as $doc) {
            $parts = $doc->parts()->whereIn('part_name', ['body_paragraph1', 'body_paragraph2', 'body_hari', 'body_tanggal', 'body_waktu', 'body_tempat'])->get();
            
            foreach ($parts as $part) {
                // Check if styling parts already exist
                $boldExists = $doc->parts()->where('part_name', $part->part_name . '_bold')->exists();
                
                if (!$boldExists) {
                    // Add styling parts for each body field
                    $doc->parts()->create([
                        'part_name' => $part->part_name . '_bold',
                        'content' => '0',
                        'order' => $part->order,
                    ]);
                    $doc->parts()->create([
                        'part_name' => $part->part_name . '_italic',
                        'content' => '0',
                        'order' => $part->order,
                    ]);
                    $doc->parts()->create([
                        'part_name' => $part->part_name . '_underline',
                        'content' => '0',
                        'order' => $part->order,
                    ]);
                    
                    // Add font size for all body fields
                    $doc->parts()->create([
                        'part_name' => $part->part_name . '_font_size',
                        'content' => '10px',
                        'order' => $part->order,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete the added styling parts
        $documents = Document::all();
        
        foreach ($documents as $doc) {
            $doc->parts()->whereIn('part_name', [
                'body_paragraph1_bold', 'body_paragraph1_italic', 'body_paragraph1_underline', 'body_paragraph1_font_size',
                'body_paragraph2_bold', 'body_paragraph2_italic', 'body_paragraph2_underline', 'body_paragraph2_font_size',
                'body_hari_bold', 'body_hari_italic', 'body_hari_underline', 'body_hari_font_size',
                'body_tanggal_bold', 'body_tanggal_italic', 'body_tanggal_underline', 'body_tanggal_font_size',
                'body_waktu_bold', 'body_waktu_italic', 'body_waktu_underline', 'body_waktu_font_size',
                'body_tempat_bold', 'body_tempat_italic', 'body_tempat_underline', 'body_tempat_font_size',
            ])->delete();
        }
    }
};
