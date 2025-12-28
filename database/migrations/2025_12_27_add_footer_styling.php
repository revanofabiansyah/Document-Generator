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
        // Add styling fields to existing documents' footer parts
        $documents = Document::all();
        
        foreach ($documents as $doc) {
            $footerFields = [
                'footer_jabatan_kiri',
                'footer_nama_kiri',
                'footer_jabatan_kanon',
                'footer_nama_kanon',
                'footer_jabatan_opsional',
                'footer_nama_opsional'
            ];
            
            foreach ($footerFields as $fieldName) {
                // Check if styling parts already exist
                $boldExists = $doc->parts()->where('part_name', $fieldName . '_bold')->exists();
                
                if (!$boldExists) {
                    // Add styling parts for each footer field
                    $doc->parts()->create([
                        'part_name' => $fieldName . '_bold',
                        'content' => '0',
                        'order' => 0,
                    ]);
                    $doc->parts()->create([
                        'part_name' => $fieldName . '_italic',
                        'content' => '0',
                        'order' => 0,
                    ]);
                    $doc->parts()->create([
                        'part_name' => $fieldName . '_underline',
                        'content' => '0',
                        'order' => 0,
                    ]);
                    
                    // Add font size for all footer fields
                    $doc->parts()->create([
                        'part_name' => $fieldName . '_font_size',
                        'content' => '9px',
                        'order' => 0,
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
        // Remove styling parts created by this migration
        $footerFields = [
            'footer_jabatan_kiri',
            'footer_nama_kiri',
            'footer_jabatan_kanon',
            'footer_nama_kanon',
            'footer_jabatan_opsional',
            'footer_nama_opsional'
        ];
        
        foreach ($footerFields as $fieldName) {
            foreach (['_bold', '_italic', '_underline', '_font_size'] as $suffix) {
                \DB::table('document_parts')
                    ->where('part_name', $fieldName . $suffix)
                    ->delete();
            }
        }
    }
};
