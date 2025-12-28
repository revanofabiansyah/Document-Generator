@extends('layouts.admin')

@section('content')

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 font-weight-bold text-gray-800">Isi Dokumen: {{ $document->title }}</h2>
        <a href="{{ route('documents.user.list') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    <p class="text-muted">Tipe: {{ ucfirst(str_replace('_', ' ', $document->template_type)) }}</p>
</div>

<div class="row">
    <!-- Left: Input Section -->
    <div class="col-lg-6">
        <form id="fillDocForm" method="POST" action="{{ route('documents.user.save', $document) }}" enctype="multipart/form-data">
            @csrf

            <!-- Kop Surat Upload Section -->
            <div class="card mb-3">
                <div class="card-header bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-image me-2"></i>Upload Gambar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Kop Left -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-image me-2"></i>Gambar Kop (Kiri)</label>
                            <div id="kopLeftThumb" style="border: 2px dashed #0d6efd; padding: 15px; text-align: center; background: #f0f7ff; height: 100px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; border-radius: 4px;">
                                <span class="text-muted" id="kopLeftText">No image</span>
                            </div>
                            <input type="file" name="kop_left" id="kopLeftFile" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'kopLeftThumb', 'kopLeftText')">
                            <small class="text-muted d-block mt-1">Max 2MB, JPG/PNG</small>
                        </div>

                        <!-- Kop Right -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-image me-2"></i>Gambar Kop (Kanan)</label>
                            <div id="kopRightThumb" style="border: 2px dashed #0d6efd; padding: 15px; text-align: center; background: #f0f7ff; height: 100px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; border-radius: 4px;">
                                <span class="text-muted" id="kopRightText">No image</span>
                            </div>
                            <input type="file" name="kop_right" id="kopRightFile" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'kopRightThumb', 'kopRightText')">
                            <small class="text-muted d-block mt-1">Max 2MB, JPG/PNG</small>
                        </div>

                        <!-- Signature -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fas fa-file-signature me-2"></i>Gambar Tandatangan</label>
                            <div id="signatureThumb" style="border: 2px dashed #ffa500; padding: 15px; text-align: center; background: #fff8f0; height: 100px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; border-radius: 4px;">
                                <span class="text-muted" id="signatureText">No image</span>
                            </div>
                            <input type="file" name="signature" id="signatureFile" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'signatureThumb', 'signatureText')">
                            <small class="text-muted d-block mt-1">Max 2MB, JPG/PNG</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Text Input Sections -->
            @php
                $parts = [
                    'kop' => ['label' => 'Kop Surat', 'icon' => 'fa-heading', 'color' => 'primary'],
                    'header' => ['label' => 'Header (Judul & Nomor)', 'icon' => 'fa-square', 'color' => 'info'],
                    'body' => ['label' => 'Isi Dokumen (Body)', 'icon' => 'fa-align-left', 'color' => 'success'],
                    'footer' => ['label' => 'Footer (Tandatangan)', 'icon' => 'fa-certificate', 'color' => 'warning']
                ];
            @endphp

            @foreach($parts as $partKey => $partInfo)
            <div class="card mb-3">
                <div class="card-header bg-{{ $partInfo['color'] }}">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas {{ $partInfo['icon'] }} me-2"></i>{{ $partInfo['label'] }}
                    </h6>
                </div>
                <div class="card-body">
                    <textarea name="content_{{ $partKey }}" id="content_{{ $partKey }}" class="form-control" rows="4" placeholder="Tulis konten untuk bagian {{ strtolower($partInfo['label']) }}...">{{ $document->parts->where('part_name', $partKey)->first()?->content ?? '' }}</textarea>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-lightbulb me-1"></i>
                        Klik di preview untuk melihat hasil secara real-time
                    </small>
                </div>
            </div>
            @endforeach

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Simpan Dokumen
                </button>
            </div>
        </form>
    </div>

    <!-- Right: Preview Section -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-eye me-2"></i>Preview Dokumen
                </h6>
            </div>
            <div class="card-body" style="max-height: 800px; overflow-y: auto;">
                <div id="docPreview" style="border: 3px solid #0d6efd; padding: 30px; background-color: #fff;">
                    
                    <!-- Display Document Parts from Database -->
                    @foreach($document->parts as $part)
                        @if($part->part_name === 'kop')
                        <div class="kop-section" style="border: 2px dashed #0d6efd; padding: 20px; margin-bottom: 20px; background-color: #fff; text-align: center; min-height: 80px; display: flex; align-items: center; justify-content: center;">
                            <div style="font-size: 12px; color: #333; white-space: pre-wrap; line-height: 1.6;">
                                {{ $part->content ?? '(Kop surat kosong)' }}
                            </div>
                        </div>
                        @elseif($part->part_name === 'header')
                        <div class="header-section" style="border: 2px dashed #999; padding: 15px; margin-bottom: 20px; background-color: #fff; text-align: center; min-height: 70px; display: flex; align-items: center; justify-content: center;">
                            <div style="font-size: 13px; color: #333; white-space: pre-wrap; line-height: 1.6;">
                                {{ $part->content ?? '(Header kosong)' }}
                            </div>
                        </div>
                        @elseif($part->part_name === 'body')
                        <div class="body-section" style="border: 2px dashed #999; padding: 15px; margin-bottom: 20px; background-color: #fff; min-height: 200px; text-align: left; line-height: 1.6;">
                            <div style="font-size: 12px; color: #333; white-space: pre-wrap;">
                                {{ $part->content ?? '(Body kosong)' }}
                            </div>
                        </div>
                        @elseif($part->part_name === 'footer')
                        <div class="footer-section" style="border: 2px dashed #999; padding: 15px; background-color: #fff; min-height: 150px; text-align: center;">
                            <div style="font-size: 11px; color: #333; white-space: pre-wrap; line-height: 1.6;">
                                {{ $part->content ?? '(Footer kosong)' }}
                            </div>
                        </div>
                        @endif
                    @endforeach

                </div>

                <div class="alert alert-info mt-3" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Catatan:</strong> Ini adalah preview dari template yang telah di-setup oleh admin. Input teks Anda di sebelah kiri akan disimpan dalam database dokumen.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId, textId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            const textEl = document.getElementById(textId);
            preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px;">';
            textEl.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Initialize preview on page load
document.addEventListener('DOMContentLoaded', function() {
    // No need to update preview dynamically - it's now read-only and loaded from database
});
</script>

<style>
    .preview-img {
        max-width: 100%;
        max-height: 100px;
        object-fit: contain;
        border-radius: 4px;
    }
</style>

@endsection
