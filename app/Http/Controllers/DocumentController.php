<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentPart;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    // Halaman Input Document (list & create)
    public function inputDocument()
    {
        $documents = auth()->user()->documents()->orderBy('created_at', 'desc')->get();
        return view('admin.documents.input', compact('documents'));
    }

    // Store dokumen baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'template_type' => 'required|in:surat_undangan,surat_pengumuman,surat_keterangan',
        ]);

        $document = auth()->user()->documents()->create([
            'title' => $request->title,
            'template_type' => $request->template_type,
            'description' => $request->description ?? '',
        ]);

        // Buat document parts default dengan granular field-level parts
        $parts = [
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
            ['part_name' => 'footer_jabatan_kanan', 'content' => '', 'order' => 4],
            ['part_name' => 'footer_nama_kanan', 'content' => '', 'order' => 4],
            ['part_name' => 'footer_jabatan_opsional', 'content' => '', 'order' => 4],
            ['part_name' => 'footer_nama_opsional', 'content' => '', 'order' => 4],
        ];

        foreach ($parts as $part) {
            $document->parts()->create($part);
        }

        return redirect()->route('documents.input')->with('success', 'Dokumen baru berhasil dibuat!');
    }

    // Halaman Edit Document
    public function editDocument()
    {
        $documents = auth()->user()->documents()->orderBy('created_at', 'desc')->get();
        return view('admin.documents.edit', compact('documents'));
    }

    // Show edit form untuk dokumen tertentu
    public function show(Document $document)
    {
        // Cek apakah dokumen milik user yang login
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        // Return JSON if requested (for AJAX calls)
        if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
            $layoutSettings = null;
            if ($document->layout_settings) {
                $layoutSettings = json_decode($document->layout_settings, true);
            }
            
            return response()->json([
                'id' => $document->id,
                'title' => $document->title,
                'layout_settings' => $layoutSettings,
            ]);
        }

        return view('admin.documents.editor', compact('document'));
    }

    // Update document parts
    public function updatePart(Request $request, DocumentPart $documentPart)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $documentPart->update([
            'content' => $request->content,
        ]);

        return response()->json(['success' => true, 'message' => 'Bagian dokumen berhasil diperbarui!']);
    }

    // Delete document
    public function destroy(Document $document)
    {
        // Cek apakah dokumen milik user yang login
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        $document->delete();
        return redirect()->route('documents.input')->with('success', 'Dokumen berhasil dihapus!');
    }

    // Update document name only
    public function updateName(Request $request, Document $document)
    {
        // Cek apakah dokumen milik user yang login
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $document->update([
            'title' => $request->title,
        ]);

        return redirect()->route('documents.input')->with('success', 'Nama dokumen berhasil diperbarui!');
    }

    // Publish document
    public function publish(Document $document)
    {
        // Cek apakah dokumen milik user yang login
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        $document->update(['is_published' => true]);

        return redirect()->route('documents.editor', $document)->with('success', 'Dokumen berhasil dipublikasikan! User sekarang bisa mengisinya.');
    }

    // Delete document part
    public function deletePart(DocumentPart $documentPart)
    {
        if ($documentPart->document->user_id !== auth()->id()) {
            abort(403);
        }

        $documentPart->delete();
        return response()->json(['success' => true, 'message' => 'Bagian dokumen berhasil dihapus!']);
    }

    // Save layout customization settings
    public function saveLayout(Request $request, Document $document)
    {
        // Cek apakah dokumen milik user yang login
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        $document->update([
            'layout_settings' => json_encode($request->all()),
        ]);

        return response()->json(['success' => true, 'message' => 'Layout settings berhasil disimpan!']);
    }
}

