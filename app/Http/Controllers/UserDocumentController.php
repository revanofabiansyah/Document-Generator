<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentPart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List semua dokumen yang bisa diisi user (published documents milik user)
     */
    public function list(User $user)
    {
        // Cek apakah user yang akses adalah user yang sesuai dengan URL
        if (auth()->id() !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Get published documents (yang bisa diisi user)
        $documents = Document::where('is_published', true)
            ->with('parts')
            ->paginate(12);

        // Get user's own documents (draft/in-progress)
        $userDocuments = Document::where('user_id', $user->id)
            ->where('is_published', false)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('user.documents-list', compact('documents', 'user', 'userDocuments'));
    }

    /**
     * Start filling a template - create user document from template
     */
    public function startFilling(User $user, $templateId)
    {
        // Cek apakah user yang akses adalah user yang sesuai dengan URL
        if (auth()->id() !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Get template (harus published)
        $template = Document::where('id', $templateId)
            ->where('is_published', true)
            ->firstOrFail();

        // Selalu buat dokumen baru (jangan reuse)
        $userDocument = Document::create([
            'user_id' => $user->id,
            'title' => $template->title,
            'template_type' => $template->template_type,
            'description' => $template->description,
            'is_published' => false,
            'current_step' => 1,
            'layout_settings' => $template->layout_settings,
        ]);

        // Copy SEMUA parts dari template (termasuk font size, bold, italic, dll)
        // Tapi reset content untuk field input (bukan meta)
        $contentFields = [
            'kop_instansi', 'kop_nama', 'kop_alamat', 'kop_telp', 'kop_email',
            'header_tempat_tanggal', 'header_judul', 'header_nomor', 'header_lampiran', 'header_perihal', 'header_body',
            'body_paragraph1', 'body_hari', 'body_tanggal', 'body_waktu', 'body_tempat', 'body_paragraph2',
            'footer_jabatan_kiri', 'footer_nama_kiri', 'footer_jabatan_kanan', 'footer_nama_kanan', 'footer_jabatan_opsional', 'footer_nama_opsional'
        ];

        foreach ($template->parts as $part) {
            $content = $part->content;
            
            // Jika field adalah content field (bukan font size/bold/italic), reset ke empty
            if (in_array($part->part_name, $contentFields)) {
                $content = '';
            }
            
            $userDocument->parts()->create([
                'part_name' => $part->part_name,
                'content' => $content,
                'order' => $part->order,
            ]);
        }

        // Redirect ke fill form dengan dokumen user
        return redirect()->route('documents.user.fill', ['user' => $user->name, 'document' => $userDocument]);
    }

    /**
     * Tampilkan form untuk mengisi dokumen (Wizard dengan 5 steps)
     */
    public function fill(User $user, Document $document)
    {
        // Cek apakah user yang akses adalah user yang sesuai dengan URL
        if (auth()->id() !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Load document dengan parts
        $document->load('parts');
        
        // Get current step dari query param, session flash, atau dari database
        $currentStep = request()->query('step') ?? session('step') ?? $document->current_step ?? 1;
        
        // Group parts by part_name untuk struktur form
        $documentParts = $document->parts->groupBy('part_name')->map(function($parts) {
            return $parts->first()->content;
        });

        // Decode layout_settings jika ada
        $layoutSettings = $document->layout_settings ? json_decode($document->layout_settings, true) : [];
        
        return view('user.document-wizard', compact('document', 'user', 'currentStep', 'documentParts', 'layoutSettings'));
    }

    /**
     * Simpan dokumen yang sudah diisi user
     */
    public function save(Request $request, User $user, Document $document)
    {
        // Cek apakah user yang akses adalah user yang sesuai dengan URL
        if (auth()->id() !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Get current step from request or default to 1
        $currentStep = $request->input('current_step', 1);
        
        // Validate input - banyak field sesuai dengan wizard
        $validated = $request->validate([
            // Kop Surat
            'kop_instansi' => 'nullable|string|max:500',
            'kop_instansi_bold' => 'nullable|in:0,1',
            'kop_instansi_italic' => 'nullable|in:0,1',
            'kop_instansi_underline' => 'nullable|in:0,1',
            'kop_nama' => 'nullable|string|max:500',
            'kop_nama_bold' => 'nullable|in:0,1',
            'kop_nama_italic' => 'nullable|in:0,1',
            'kop_nama_underline' => 'nullable|in:0,1',
            'kop_alamat' => 'nullable|string|max:500',
            'kop_alamat_bold' => 'nullable|in:0,1',
            'kop_alamat_italic' => 'nullable|in:0,1',
            'kop_alamat_underline' => 'nullable|in:0,1',
            'kop_telp' => 'nullable|string|max:50',
            'kop_telp_bold' => 'nullable|in:0,1',
            'kop_telp_italic' => 'nullable|in:0,1',
            'kop_telp_underline' => 'nullable|in:0,1',
            'kop_email' => 'nullable|email|max:100',
            'kop_email_bold' => 'nullable|in:0,1',
            'kop_email_italic' => 'nullable|in:0,1',
            'kop_email_underline' => 'nullable|in:0,1',
            'kop_left' => 'nullable|image|max:2048',
            'kop_right' => 'nullable|image|max:2048',
            'kop_instansi_font_size' => 'nullable|string',
            'kop_nama_font_size' => 'nullable|string',
            'kop_alamat_font_size' => 'nullable|string',
            'kop_telp_font_size' => 'nullable|string',
            'kop_email_font_size' => 'nullable|string',
            'current_step' => 'nullable|integer',

            // Header
            'header_tempat_tanggal' => 'nullable|string|max:300',
            'header_tempat_tanggal_bold' => 'nullable|in:0,1',
            'header_tempat_tanggal_italic' => 'nullable|in:0,1',
            'header_tempat_tanggal_underline' => 'nullable|in:0,1',
            'header_tempat_tanggal_font_size' => 'nullable|string',
            'header_judul' => 'nullable|string|max:300',
            'header_judul_bold' => 'nullable|in:0,1',
            'header_judul_italic' => 'nullable|in:0,1',
            'header_judul_underline' => 'nullable|in:0,1',
            'header_judul_font_size' => 'nullable|string',
            'header_nomor' => 'nullable|string|max:100',
            'header_nomor_bold' => 'nullable|in:0,1',
            'header_nomor_italic' => 'nullable|in:0,1',
            'header_nomor_underline' => 'nullable|in:0,1',
            'header_nomor_font_size' => 'nullable|string',
            'header_lampiran' => 'nullable|string|max:100',
            'header_lampiran_bold' => 'nullable|in:0,1',
            'header_lampiran_italic' => 'nullable|in:0,1',
            'header_lampiran_underline' => 'nullable|in:0,1',
            'header_perihal' => 'nullable|string|max:300',
            'header_perihal_bold' => 'nullable|in:0,1',
            'header_perihal_italic' => 'nullable|in:0,1',
            'header_perihal_underline' => 'nullable|in:0,1',
            'header_body' => 'nullable|string|max:2000',
            'header_body_bold' => 'nullable|in:0,1',
            'header_body_italic' => 'nullable|in:0,1',
            'header_body_underline' => 'nullable|in:0,1',
            'header_body_font_size' => 'nullable|string',

            // Body
            'body_paragraph1' => 'nullable|string|max:3000',
            'body_paragraph1_bold' => 'nullable|in:0,1',
            'body_paragraph1_italic' => 'nullable|in:0,1',
            'body_paragraph1_underline' => 'nullable|in:0,1',
            'body_paragraph1_font_size' => 'nullable|string',
            'body_hari' => 'nullable|string|max:50',
            'body_hari_bold' => 'nullable|in:0,1',
            'body_hari_italic' => 'nullable|in:0,1',
            'body_hari_underline' => 'nullable|in:0,1',
            'body_hari_font_size' => 'nullable|string',
            'body_tanggal' => 'nullable|string|max:50',
            'body_tanggal_bold' => 'nullable|in:0,1',
            'body_tanggal_italic' => 'nullable|in:0,1',
            'body_tanggal_underline' => 'nullable|in:0,1',
            'body_tanggal_font_size' => 'nullable|string',
            'body_waktu' => 'nullable|string|max:100',
            'body_waktu_bold' => 'nullable|in:0,1',
            'body_waktu_italic' => 'nullable|in:0,1',
            'body_waktu_underline' => 'nullable|in:0,1',
            'body_waktu_font_size' => 'nullable|string',
            'body_tempat' => 'nullable|string|max:300',
            'body_tempat_bold' => 'nullable|in:0,1',
            'body_tempat_italic' => 'nullable|in:0,1',
            'body_tempat_underline' => 'nullable|in:0,1',
            'body_tempat_font_size' => 'nullable|string',
            'body_paragraph2' => 'nullable|string|max:3000',
            'body_paragraph2_bold' => 'nullable|in:0,1',
            'body_paragraph2_italic' => 'nullable|in:0,1',
            'body_paragraph2_underline' => 'nullable|in:0,1',
            'body_paragraph2_font_size' => 'nullable|string',

            // Footer
            'footer_jabatan_kiri' => 'nullable|string|max:200',
            'footer_jabatan_kiri_bold' => 'nullable|in:0,1',
            'footer_jabatan_kiri_italic' => 'nullable|in:0,1',
            'footer_jabatan_kiri_underline' => 'nullable|in:0,1',
            'footer_jabatan_kiri_font_size' => 'nullable|string',
            'footer_nama_kiri' => 'nullable|string|max:200',
            'footer_nama_kiri_bold' => 'nullable|in:0,1',
            'footer_nama_kiri_italic' => 'nullable|in:0,1',
            'footer_nama_kiri_underline' => 'nullable|in:0,1',
            'footer_nama_kiri_font_size' => 'nullable|string',
            'footer_jabatan_kanan' => 'nullable|string|max:200',
            'footer_jabatan_kanan_bold' => 'nullable|in:0,1',
            'footer_jabatan_kanan_italic' => 'nullable|in:0,1',
            'footer_jabatan_kanan_underline' => 'nullable|in:0,1',
            'footer_jabatan_kanan_font_size' => 'nullable|string',
            'footer_nama_kanan' => 'nullable|string|max:200',
            'footer_nama_kanan_bold' => 'nullable|in:0,1',
            'footer_nama_kanan_italic' => 'nullable|in:0,1',
            'footer_nama_kanan_underline' => 'nullable|in:0,1',
            'footer_nama_kanan_font_size' => 'nullable|string',
            'footer_jabatan_opsional' => 'nullable|string|max:200',
            'footer_jabatan_opsional_bold' => 'nullable|in:0,1',
            'footer_jabatan_opsional_italic' => 'nullable|in:0,1',
            'footer_jabatan_opsional_underline' => 'nullable|in:0,1',
            'footer_jabatan_opsional_font_size' => 'nullable|string',
            'footer_nama_opsional' => 'nullable|string|max:200',
            'footer_nama_opsional_bold' => 'nullable|in:0,1',
            'footer_nama_opsional_italic' => 'nullable|in:0,1',
            'footer_nama_opsional_underline' => 'nullable|in:0,1',
            'footer_nama_opsional_font_size' => 'nullable|string',
            'footer_signature_kiri' => 'nullable|image|max:2048',
            'footer_signature_kanan' => 'nullable|image|max:2048',
            'footer_signature_opsional' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        // Simpan semua bagian sebagai document parts
        // Kop Surat
        $this->saveOrUpdatePart($document, 'kop_instansi', $validated['kop_instansi'] ?? '');
        $this->saveOrUpdatePart($document, 'kop_instansi_bold', $validated['kop_instansi_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_instansi_italic', $validated['kop_instansi_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_instansi_underline', $validated['kop_instansi_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_instansi_font_size', $validated['kop_instansi_font_size'] ?? '16px');
        
        $this->saveOrUpdatePart($document, 'kop_nama', $validated['kop_nama'] ?? '');
        $this->saveOrUpdatePart($document, 'kop_nama_bold', $validated['kop_nama_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_nama_italic', $validated['kop_nama_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_nama_underline', $validated['kop_nama_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_nama_font_size', $validated['kop_nama_font_size'] ?? '18px');
        
        $this->saveOrUpdatePart($document, 'kop_alamat', $validated['kop_alamat'] ?? '');
        $this->saveOrUpdatePart($document, 'kop_alamat_bold', $validated['kop_alamat_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_alamat_italic', $validated['kop_alamat_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_alamat_underline', $validated['kop_alamat_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_alamat_font_size', $validated['kop_alamat_font_size'] ?? '12px');
        
        $this->saveOrUpdatePart($document, 'kop_telp', $validated['kop_telp'] ?? '');
        $this->saveOrUpdatePart($document, 'kop_telp_bold', $validated['kop_telp_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_telp_italic', $validated['kop_telp_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_telp_underline', $validated['kop_telp_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_telp_font_size', $validated['kop_telp_font_size'] ?? '12px');
        
        $this->saveOrUpdatePart($document, 'kop_email', $validated['kop_email'] ?? '');
        $this->saveOrUpdatePart($document, 'kop_email_bold', $validated['kop_email_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_email_italic', $validated['kop_email_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_email_underline', $validated['kop_email_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'kop_email_font_size', $validated['kop_email_font_size'] ?? '12px');

        // Header
        $this->saveOrUpdatePart($document, 'header_tempat_tanggal', $validated['header_tempat_tanggal'] ?? '');
        $this->saveOrUpdatePart($document, 'header_tempat_tanggal_bold', $validated['header_tempat_tanggal_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_tempat_tanggal_italic', $validated['header_tempat_tanggal_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_tempat_tanggal_underline', $validated['header_tempat_tanggal_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_tempat_tanggal_font_size', $validated['header_tempat_tanggal_font_size'] ?? '12px');
        
        $this->saveOrUpdatePart($document, 'header_judul', $validated['header_judul'] ?? '');
        $this->saveOrUpdatePart($document, 'header_judul_bold', $validated['header_judul_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_judul_italic', $validated['header_judul_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_judul_underline', $validated['header_judul_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_judul_font_size', $validated['header_judul_font_size'] ?? '14px');
        
        $this->saveOrUpdatePart($document, 'header_nomor', $validated['header_nomor'] ?? '');
        $this->saveOrUpdatePart($document, 'header_nomor_bold', $validated['header_nomor_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_nomor_italic', $validated['header_nomor_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_nomor_underline', $validated['header_nomor_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_nomor_font_size', $validated['header_nomor_font_size'] ?? '11px');
        
        $this->saveOrUpdatePart($document, 'header_lampiran', $validated['header_lampiran'] ?? '');
        $this->saveOrUpdatePart($document, 'header_lampiran_bold', $validated['header_lampiran_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_lampiran_italic', $validated['header_lampiran_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_lampiran_underline', $validated['header_lampiran_underline'] ?? '0');
        
        $this->saveOrUpdatePart($document, 'header_perihal', $validated['header_perihal'] ?? '');
        $this->saveOrUpdatePart($document, 'header_perihal_bold', $validated['header_perihal_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_perihal_italic', $validated['header_perihal_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_perihal_underline', $validated['header_perihal_underline'] ?? '0');
        
        $this->saveOrUpdatePart($document, 'header_body', $validated['header_body'] ?? '');
        $this->saveOrUpdatePart($document, 'header_body_bold', $validated['header_body_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_body_italic', $validated['header_body_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_body_underline', $validated['header_body_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'header_body_font_size', $validated['header_body_font_size'] ?? '11px');

        // Body
        $this->saveOrUpdatePart($document, 'body_paragraph1', $validated['body_paragraph1'] ?? '');
        $this->saveOrUpdatePart($document, 'body_paragraph1_bold', $validated['body_paragraph1_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_paragraph1_italic', $validated['body_paragraph1_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_paragraph1_underline', $validated['body_paragraph1_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_paragraph1_font_size', $validated['body_paragraph1_font_size'] ?? '10px');
        
        $this->saveOrUpdatePart($document, 'body_hari', $validated['body_hari'] ?? '');
        $this->saveOrUpdatePart($document, 'body_hari_bold', $validated['body_hari_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_hari_italic', $validated['body_hari_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_hari_underline', $validated['body_hari_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_hari_font_size', $validated['body_hari_font_size'] ?? '10px');
        
        $this->saveOrUpdatePart($document, 'body_tanggal', $validated['body_tanggal'] ?? '');
        $this->saveOrUpdatePart($document, 'body_tanggal_bold', $validated['body_tanggal_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_tanggal_italic', $validated['body_tanggal_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_tanggal_underline', $validated['body_tanggal_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_tanggal_font_size', $validated['body_tanggal_font_size'] ?? '10px');
        
        $this->saveOrUpdatePart($document, 'body_waktu', $validated['body_waktu'] ?? '');
        $this->saveOrUpdatePart($document, 'body_waktu_bold', $validated['body_waktu_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_waktu_italic', $validated['body_waktu_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_waktu_underline', $validated['body_waktu_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_waktu_font_size', $validated['body_waktu_font_size'] ?? '10px');
        
        $this->saveOrUpdatePart($document, 'body_tempat', $validated['body_tempat'] ?? '');
        $this->saveOrUpdatePart($document, 'body_tempat_bold', $validated['body_tempat_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_tempat_italic', $validated['body_tempat_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_tempat_underline', $validated['body_tempat_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_tempat_font_size', $validated['body_tempat_font_size'] ?? '10px');
        
        $this->saveOrUpdatePart($document, 'body_paragraph2', $validated['body_paragraph2'] ?? '');
        $this->saveOrUpdatePart($document, 'body_paragraph2_bold', $validated['body_paragraph2_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_paragraph2_italic', $validated['body_paragraph2_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_paragraph2_underline', $validated['body_paragraph2_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'body_paragraph2_font_size', $validated['body_paragraph2_font_size'] ?? '10px');

        // Footer
        $this->saveOrUpdatePart($document, 'footer_jabatan_kiri', $validated['footer_jabatan_kiri'] ?? '');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kiri_bold', $validated['footer_jabatan_kiri_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kiri_italic', $validated['footer_jabatan_kiri_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kiri_underline', $validated['footer_jabatan_kiri_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kiri_font_size', $validated['footer_jabatan_kiri_font_size'] ?? '9px');
        
        $this->saveOrUpdatePart($document, 'footer_nama_kiri', $validated['footer_nama_kiri'] ?? '');
        $this->saveOrUpdatePart($document, 'footer_nama_kiri_bold', $validated['footer_nama_kiri_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_kiri_italic', $validated['footer_nama_kiri_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_kiri_underline', $validated['footer_nama_kiri_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_kiri_font_size', $validated['footer_nama_kiri_font_size'] ?? '9px');
        
        $this->saveOrUpdatePart($document, 'footer_jabatan_kanan', $validated['footer_jabatan_kanan'] ?? '');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kanan_bold', $validated['footer_jabatan_kanan_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kanan_italic', $validated['footer_jabatan_kanan_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kanan_underline', $validated['footer_jabatan_kanan_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_kanan_font_size', $validated['footer_jabatan_kanan_font_size'] ?? '9px');
        
        $this->saveOrUpdatePart($document, 'footer_nama_kanan', $validated['footer_nama_kanan'] ?? '');
        $this->saveOrUpdatePart($document, 'footer_nama_kanan_bold', $validated['footer_nama_kanan_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_kanan_italic', $validated['footer_nama_kanan_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_kanan_underline', $validated['footer_nama_kanan_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_kanan_font_size', $validated['footer_nama_kanan_font_size'] ?? '9px');
        
        $this->saveOrUpdatePart($document, 'footer_jabatan_opsional', $validated['footer_jabatan_opsional'] ?? '');
        $this->saveOrUpdatePart($document, 'footer_jabatan_opsional_bold', $validated['footer_jabatan_opsional_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_opsional_italic', $validated['footer_jabatan_opsional_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_opsional_underline', $validated['footer_jabatan_opsional_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_jabatan_opsional_font_size', $validated['footer_jabatan_opsional_font_size'] ?? '9px');
        
        $this->saveOrUpdatePart($document, 'footer_nama_opsional', $validated['footer_nama_opsional'] ?? '');
        $this->saveOrUpdatePart($document, 'footer_nama_opsional_bold', $validated['footer_nama_opsional_bold'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_opsional_italic', $validated['footer_nama_opsional_italic'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_opsional_underline', $validated['footer_nama_opsional_underline'] ?? '0');
        $this->saveOrUpdatePart($document, 'footer_nama_opsional_font_size', $validated['footer_nama_opsional_font_size'] ?? '9px');

        // Handle image uploads
        if ($request->hasFile('kop_left')) {
            $path = $request->file('kop_left')->store('documents/images', 'public');
            $document->update(['kop_left_image' => $path]);
        }

        if ($request->hasFile('kop_right')) {
            $path = $request->file('kop_right')->store('documents/images', 'public');
            $document->update(['kop_right_image' => $path]);
        }

        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('documents/images', 'public');
            $document->update(['signature_image' => $path]);
        }

        // Save current step to document
        $document->update(['current_step' => $currentStep]);

        // Mark as updated
        $document->touch();

        // Redirect kembali ke halaman wizard dengan step yang sama
        return redirect()->route('documents.user.fill', ['user' => $user->name, 'document' => $document])
            ->with('step', $currentStep)
            ->with('success', 'Dokumen berhasil disimpan!');
    }

    /**
     * Helper function untuk save atau update document parts
     */
    private function saveOrUpdatePart($document, $partName, $content)
    {
        $part = $document->parts()->where('part_name', $partName)->first();
        
        if ($part) {
            $part->update(['content' => $content]);
        } else {
            $document->parts()->create([
                'part_name' => $partName,
                'content' => $content,
                'order' => 99
            ]);
        }
    }

    /**
     * Helper untuk get order dari part
     */
    private function getPartOrder($part)
    {
        $order = [
            'kop' => 1,
            'header' => 2,
            'body' => 3,
            'footer' => 4
        ];
        
        return $order[$part] ?? 99;
    }

    /**
     * Preview dokumen (bisa buat PDF nanti)
     */
    public function preview(Document $document)
    {
        $document->load('parts');
        
        return view('user.preview-document', compact('document'));
    }

    /**
     * Download dokumen sebagai PDF
     */
    public function download(Document $document)
    {
        // Implementation untuk generate PDF nanti
        // Bisa pakai mPDF atau similar
        
        return response()->download('path/to/pdf');
    }

    /**
     * Hapus dokumen milik user
     */
    public function delete(User $user, Document $document)
    {
        // Cek apakah user yang akses adalah user yang sesuai dengan URL
        if (auth()->id() !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Hapus document parts
        $document->parts()->delete();

        // Hapus document
        $document->delete();

        return redirect()->route('documents.user.list', ['user' => $user->name])
            ->with('success', 'Dokumen berhasil dihapus!');
    }
}
