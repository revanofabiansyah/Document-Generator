<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Document;
use App\Models\DocumentPart;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For each document that has old generic parts (kop, header, body, footer),
        // replace with granular field-level parts
        $documents = Document::all();
        
        foreach ($documents as $doc) {
            // Check if document still has old generic parts
            $hasOldParts = $doc->parts()->whereIn('part_name', ['kop', 'header', 'body', 'footer'])->exists();
            
            if ($hasOldParts) {
                // Delete old generic parts
                $doc->parts()->whereIn('part_name', ['kop', 'header', 'body', 'footer'])->delete();
                
                // Create granular parts
                $newParts = [
                    // Kop Surat
                    ['part_name' => 'kop_instansi', 'content' => '', 'order' => 1],
                    ['part_name' => 'kop_instansi_bold', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_instansi_italic', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_instansi_underline', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_instansi_font_size', 'content' => '12px', 'order' => 1],
                    
                    ['part_name' => 'kop_nama', 'content' => '', 'order' => 1],
                    ['part_name' => 'kop_nama_bold', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_nama_italic', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_nama_underline', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_nama_font_size', 'content' => '10px', 'order' => 1],
                    
                    ['part_name' => 'kop_alamat', 'content' => '', 'order' => 1],
                    ['part_name' => 'kop_alamat_bold', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_alamat_italic', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_alamat_underline', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_alamat_font_size', 'content' => '9px', 'order' => 1],
                    
                    ['part_name' => 'kop_telp', 'content' => '', 'order' => 1],
                    ['part_name' => 'kop_telp_bold', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_telp_italic', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_telp_underline', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_telp_font_size', 'content' => '9px', 'order' => 1],
                    
                    ['part_name' => 'kop_email', 'content' => '', 'order' => 1],
                    ['part_name' => 'kop_email_bold', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_email_italic', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_email_underline', 'content' => '0', 'order' => 1],
                    ['part_name' => 'kop_email_font_size', 'content' => '9px', 'order' => 1],
                    
                    // Header
                    ['part_name' => 'header_tempat_tanggal', 'content' => '', 'order' => 2],
                    ['part_name' => 'header_tempat_tanggal_bold', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_tempat_tanggal_italic', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_tempat_tanggal_underline', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_tempat_tanggal_font_size', 'content' => '12px', 'order' => 2],
                    
                    ['part_name' => 'header_judul', 'content' => '', 'order' => 2],
                    ['part_name' => 'header_judul_bold', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_judul_italic', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_judul_underline', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_judul_font_size', 'content' => '14px', 'order' => 2],
                    
                    ['part_name' => 'header_nomor', 'content' => '', 'order' => 2],
                    ['part_name' => 'header_nomor_bold', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_nomor_italic', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_nomor_underline', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_nomor_font_size', 'content' => '11px', 'order' => 2],
                    
                    ['part_name' => 'header_lampiran', 'content' => '', 'order' => 2],
                    ['part_name' => 'header_lampiran_bold', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_lampiran_italic', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_lampiran_underline', 'content' => '0', 'order' => 2],
                    
                    ['part_name' => 'header_perihal', 'content' => '', 'order' => 2],
                    ['part_name' => 'header_perihal_bold', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_perihal_italic', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_perihal_underline', 'content' => '0', 'order' => 2],
                    
                    ['part_name' => 'header_body', 'content' => '', 'order' => 2],
                    ['part_name' => 'header_body_bold', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_body_italic', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_body_underline', 'content' => '0', 'order' => 2],
                    ['part_name' => 'header_body_font_size', 'content' => '11px', 'order' => 2],
                    
                    // Body
                    ['part_name' => 'body_paragraph1', 'content' => '', 'order' => 3],
                    ['part_name' => 'body_hari', 'content' => '', 'order' => 3],
                    ['part_name' => 'body_tanggal', 'content' => '', 'order' => 3],
                    ['part_name' => 'body_waktu', 'content' => '', 'order' => 3],
                    ['part_name' => 'body_tempat', 'content' => '', 'order' => 3],
                    ['part_name' => 'body_paragraph2', 'content' => '', 'order' => 3],
                    
                    // Footer
                    ['part_name' => 'footer_jabatan_kiri', 'content' => '', 'order' => 4],
                    ['part_name' => 'footer_nama_kiri', 'content' => '', 'order' => 4],
                    ['part_name' => 'footer_jabatan_kanon', 'content' => '', 'order' => 4],
                    ['part_name' => 'footer_nama_kanon', 'content' => '', 'order' => 4],
                    ['part_name' => 'footer_jabatan_opsional', 'content' => '', 'order' => 4],
                    ['part_name' => 'footer_nama_opsional', 'content' => '', 'order' => 4],
                ];
                
                foreach ($newParts as $part) {
                    $doc->parts()->create($part);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably reverse this, so we don't do anything on rollback
    }
};