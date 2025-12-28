@extends('layouts.admin')

@section('content')

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 font-weight-bold text-gray-800">Edit Document</h2>
        <a href="{{ route('documents.input') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Select Document Section -->
<div class="card mb-4">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-edit me-2"></i>Choose Document
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label for="document_select" class="form-label">Pilih Dokumen</label>
                <select class="form-select" id="document_select" onchange="loadDocumentSettings()">
                    <option value="">--Choose--</option>
                    @foreach($documents as $doc)
                        <option value="{{ $doc->id }}">
                            {{ $doc->title }} 
                            @if($doc->is_published)
                                <span style="color: green;">✓ Published</span>
                            @else
                                <span style="color: orange;">● Draft</span>
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-primary w-100" onclick="chooseDocument()">
                    <i class="fas fa-arrow-right me-2"></i>Choose
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Document Settings (Hidden until document selected) -->
<div id="settingsSection" style="display: none;">

<!-- Settings: Images & Customization -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <i class="fas fa-cog me-2"></i>Document Settings - Gambar & Kustomisasi
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Kop Surat - Left Image -->
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-image me-2"></i>Gambar Kop (Kiri)</label>
                <div id="kopLeftPreview" style="border: 2px dashed #0d6efd; padding: 15px; text-align: center; background: #f0f7ff; height: 120px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; border-radius: 4px;">
                    <span class="text-muted" id="kopLeftText">No image</span>
                </div>
                <input type="file" id="kopLeftInput" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'kopLeftPreview', 'kopLeftText')">
                <small class="text-muted d-block mt-2">Max 2MB, format: JPG/PNG</small>
            </div>

            <!-- Kop Surat - Right Image -->
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-image me-2"></i>Gambar Kop (Kanan)</label>
                <div id="kopRightPreview" style="border: 2px dashed #0d6efd; padding: 15px; text-align: center; background: #f0f7ff; height: 120px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; border-radius: 4px;">
                    <span class="text-muted" id="kopRightText">No image</span>
                </div>
                <input type="file" id="kopRightInput" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'kopRightPreview', 'kopRightText')">
                <small class="text-muted d-block mt-2">Max 2MB, format: JPG/PNG</small>
            </div>

            <!-- Tandatangan / Footer Images -->
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-file-signature me-2"></i>Gambar Tandatangan</label>
                <div id="signaturePreview" style="border: 2px dashed #ffa500; padding: 15px; text-align: center; background: #fff8f0; height: 120px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; border-radius: 4px;">
                    <span class="text-muted" id="signatureText">No image</span>
                </div>
                <input type="file" id="signatureInput" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'signaturePreview', 'signatureText')">
                <small class="text-muted d-block mt-2">Max 2MB, format: JPG/PNG</small>
            </div>

            <!-- Bagian/Parts Customization -->
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-layer-group me-2"></i>Kelola Bagian</label>
                <div style="border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; background: #f8f9fa; height: 120px; overflow-y: auto;">
                    <small class="d-block mb-2 text-muted">Bagian standar: Kop, Header, Body, Footer</small>
                    <button class="btn btn-sm btn-success w-100" onclick="addNewPart()">
                        <i class="fas fa-plus me-1"></i>Tambah Bagian Custom
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Layout Customization Section -->
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <i class="fas fa-sliders-h me-2"></i><strong>Layout Customization - Atur Tata Letak</strong>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-4" id="layoutTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                    <i class="fas fa-cog me-2"></i>Umum
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kop-tab" data-bs-toggle="tab" data-bs-target="#kop-settings" type="button">
                    <i class="fas fa-heading me-2"></i>Kop Surat
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="header-tab" data-bs-toggle="tab" data-bs-target="#header-settings" type="button">
                    <i class="fas fa-square me-2"></i>Header
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="body-tab" data-bs-toggle="tab" data-bs-target="#body-settings" type="button">
                    <i class="fas fa-align-left me-2"></i>Body
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer-settings" type="button">
                    <i class="fas fa-certificate me-2"></i>Footer
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label"><i class="fas fa-font me-2"></i>Font Document</label>
                            <select class="form-select" id="documentFontFamily" onchange="updatePreview()">
                                <option value="Times New Roman">Times New Roman (Formal)</option>
                                <option value="Arial">Arial (Modern)</option>
                                <option value="Calibri">Calibri (Professional)</option>
                                <option value="Georgia">Georgia (Elegant)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label"><i class="fas fa-expand me-2"></i>Margin Halaman (px)</label>
                            <input type="number" class="form-control" id="documentMargin" value="20" min="10" max="50" onchange="updatePreview()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kop Settings -->
            <div class="tab-pane fade" id="kop-settings" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Posisi Kop</label>
                            <select class="form-select" id="kopPosition" onchange="updatePreview()">
                                <option value="top" selected>Di Atas (Top)</option>
                                <option value="bottom">Di Bawah (Bottom)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Alignment Kop</label>
                            <select class="form-select" id="kopAlignment" onchange="updatePreview()">
                                <option value="left">Kiri (Left)</option>
                                <option value="center" selected>Tengah (Center)</option>
                                <option value="right">Kanan (Right)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Tinggi Kop (px)</label>
                            <input type="range" class="form-range" id="kopHeight" value="100" min="50" max="250" oninput="document.getElementById('kopHeightValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="kopHeightValue">100</span>px</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Jarak Kop ke Isi (px)</label>
                            <input type="range" class="form-range" id="kopSpacing" value="20" min="0" max="50" oninput="document.getElementById('kopSpacingValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="kopSpacingValue">20</span>px</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Lebar Gambar Kop (px)</label>
                            <input type="range" class="form-range" id="kopImageWidth" value="60" min="30" max="200" oninput="document.getElementById('kopImageWidthValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="kopImageWidthValue">60</span>px</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tinggi Gambar Kop (px)</label>
                            <input type="range" class="form-range" id="kopImageHeight" value="80" min="30" max="200" oninput="document.getElementById('kopImageHeightValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="kopImageHeightValue">80</span>px</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Settings -->
            <div class="tab-pane fade" id="header-settings" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Alignment Header</label>
                            <select class="form-select" id="headerAlignment" onchange="updatePreview()">
                                <option value="left">Kiri (Left)</option>
                                <option value="center" selected>Tengah (Center)</option>
                                <option value="right">Kanan (Right)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Ukuran Font Header</label>
                            <select class="form-select" id="headerFontSize" onchange="updatePreview()">
                                <option value="12px">Kecil (12px)</option>
                                <option value="14px" selected>Normal (14px)</option>
                                <option value="16px">Besar (16px)</option>
                                <option value="18px">Lebih Besar (18px)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Tinggi Header (px)</label>
                            <input type="range" class="form-range" id="headerHeight" value="70" min="50" max="200" oninput="document.getElementById('headerHeightValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="headerHeightValue">70</span>px</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body Settings -->
            <div class="tab-pane fade" id="body-settings" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Alignment Body</label>
                            <select class="form-select" id="bodyAlignment" onchange="updatePreview()">
                                <option value="left" selected>Kiri (Left)</option>
                                <option value="center">Tengah (Center)</option>
                                <option value="right">Kanan (Right)</option>
                                <option value="justify">Justified</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Line Height (Spasi Baris)</label>
                            <select class="form-select" id="bodyLineHeight" onchange="updatePreview()">
                                <option value="1.4">Sempit (1.4)</option>
                                <option value="1.6" selected>Normal (1.6)</option>
                                <option value="1.8">Luas (1.8)</option>
                                <option value="2">Sangat Luas (2)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tinggi Body (px)</label>
                            <input type="range" class="form-range" id="bodyHeight" value="200" min="100" max="600" oninput="document.getElementById('bodyHeightValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="bodyHeightValue">200</span>px</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Settings -->
            <div class="tab-pane fade" id="footer-settings" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Layout Tandatangan</label>
                            <select class="form-select" id="signatureLayout" onchange="updatePreview()">
                                <option value="2col" selected>2 Kolom</option>
                                <option value="3col">3 Kolom</option>
                                <option value="4col">4 Kolom</option>
                                <option value="stack">Tumpuk Vertikal</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Alignment Footer</label>
                            <select class="form-select" id="footerAlignment" onchange="updatePreview()">
                                <option value="left">Kiri (Left)</option>
                                <option value="center" selected>Tengah (Center)</option>
                                <option value="right">Kanan (Right)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Tinggi Footer (px)</label>
                            <input type="range" class="form-range" id="footerHeight" value="150" min="100" max="300" oninput="document.getElementById('footerHeightValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="footerHeightValue">150</span>px</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lebar Signature (px)</label>
                            <input type="range" class="form-range" id="signatureWidth" value="80" min="40" max="150" oninput="document.getElementById('signatureWidthValue').textContent = this.value; updatePreview()" onchange="updatePreview()">
                            <small class="text-muted">Nilai: <span id="signatureWidthValue">80</span>px</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Info:</strong> Atur tata letak dokumen sesuai kebutuhan. Preview akan update otomatis saat Anda mengubah pengaturan. Semua customization akan tersimpan.
        </div>

        <!-- Save Layout Settings Button -->
        <button type="button" class="btn btn-success" onclick="saveLayoutSettings()">
            <i class="fas fa-save me-2"></i>Simpan Layout Settings
        </button>
    </div>
</div>

<!-- Document Layout Preview Section -->
@if($documents->count() > 0)
<div class="card">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-file me-2"></i>Structure Preview - Tata Letak Dokumen
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Left: Document Layout Visualization -->
            <div class="col-lg-7">
                <div id="layoutPreview" style="border: 3px solid #0d6efd; padding: 30px; background-color: #f8f9fa; min-height: 800px;">
                    
                    <!-- Kop Surat dengan Image Placeholders -->
                    <div class="kop-section" style="border: 2px dashed #0d6efd; padding: 20px; margin-bottom: 20px; background-color: #fff; display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 15px; align-items: center; min-height: 100px;">
                        <div id="kopLeftDisplay" style="text-align: center;">
                            <div style="border: 2px dashed #ccc; padding: 20px; background: #f0f7ff; border-radius: 4px; color: #999;">
                                <i class="fas fa-image" style="font-size: 24px;"></i><br>
                                <small>Logo Kiri</small>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <p style="margin: 0; font-size: 14px; font-weight: bold;">KOP SURAT</p>
                            <p style="margin: 5px 0 0 0; font-size: 11px; color: #999;">&lt;&lt;Instansi / Perusahaan&gt;&gt;</p>
                            <p style="margin: 5px 0 0 0; font-size: 10px; color: #ccc;">&lt;&lt;Nama Instansi/Cabang/Dinas&gt;&gt;</p>
                        </div>
                        <div id="kopRightDisplay" style="text-align: center;">
                            <div style="border: 2px dashed #ccc; padding: 20px; background: #f0f7ff; border-radius: 4px; color: #999;">
                                <i class="fas fa-image" style="font-size: 24px;"></i><br>
                                <small>Logo Kanan</small>
                            </div>
                        </div>
                    </div>

                    <!-- Header -->
                    <div class="header-section" style="border: 2px dashed #999; padding: 15px; margin-bottom: 20px; background-color: #fff; min-height: 70px;">
                        <div>
                            <p style="margin: 0; font-size: 13px; font-weight: bold;">&lt;&lt;NAMA SURAT&gt;&gt;</p>
                            <p style="margin: 5px 0 0 0; font-size: 10px; color: #999;">&lt;&lt;Nomor Surat&gt;&gt;</p>
                            <p style="margin: 3px 0 0 0; font-size: 9px; color: #ccc;">&lt;&lt;Tanggal&gt;&gt;</p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="body-section" style="border: 2px dashed #999; padding: 15px; margin-bottom: 20px; background-color: #fff; min-height: 200px;">
                        <p style="margin: 0 0 10px 0; font-size: 10px;">&lt;&lt;Paragraf Pertama&gt;&gt;</p>
                        <p style="margin: 10px 0; font-size: 10px; color: #999;">&lt;&lt;Keterangan&gt;&gt;</p>
                        <p style="margin: 10px 0; font-size: 10px; color: #999;">&lt;&lt;Isi Dokumen&gt;&gt;</p>
                        <p style="margin: 10px 0 0 0; font-size: 10px; color: #999;">&lt;&lt;Paragraf Kedua&gt;&gt;</p>
                    </div>

                    <!-- Footer dengan Tandatangan -->
                    <div class="footer-section" style="border: 2px dashed #999; padding: 15px; background-color: #fff; min-height: 150px; text-align: center;">
                        <p style="margin: 0 0 20px 0; font-size: 10px; color: #999;">&lt;&lt;Tanda Tangan&gt;&gt;</p>
                        <div id="signatureDisplay" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div style="text-align: center;">
                                <div style="border: 2px dashed #ccc; padding: 20px; background: #fff8f0; border-radius: 4px; height: 80px; display: flex; align-items: center; justify-content: center; color: #999; margin-bottom: 10px;">
                                    <div>
                                        <i class="fas fa-image" style="font-size: 24px;"></i><br>
                                        <small>Tanda Tangan 1</small>
                                    </div>
                                </div>
                                <p style="margin: 0; font-size: 9px;">&lt;&lt;Nama&gt;&gt;</p>
                            </div>
                            <div style="text-align: center;">
                                <div style="border: 2px dashed #ccc; padding: 20px; background: #fff8f0; border-radius: 4px; height: 80px; display: flex; align-items: center; justify-content: center; color: #999; margin-bottom: 10px;">
                                    <div>
                                        <i class="fas fa-image" style="font-size: 24px;"></i><br>
                                        <small>Tanda Tangan 2</small>
                                    </div>
                                </div>
                                <p style="margin: 0; font-size: 9px;">&lt;&lt;Nama&gt;&gt;</p>
                            </div>
                        </div>
                        <p style="margin: 0; font-size: 10px; color: #999;">&lt;&lt;Tanda Tangan 3&gt;&gt;</p>
                        <p style="margin: 3px 0 0 0; font-size: 9px; color: #999;">&lt;&lt;Nama&gt;&gt;</p>
                    </div>

                </div>
            </div>

            <!-- Right: Document Parts Info -->
            <div class="col-lg-5">
                <div class="card border-0 bg-dark" style="position: sticky; top: 20px;">
                    <div class="card-header bg-secondary">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-layer-group me-2"></i>Document Parts
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action part-selector bg-dark text-white border-0" data-part="kop" onclick="highlightPart('kop'); return false;" style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <span style="font-size: 20px; width: 30px;">📋</span>
                                    <div>
                                        <div class="font-weight-bold">Kop Surat</div>
                                        <small class="text-white-50">Bagian kepala dokumen dengan logo/gambar</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action part-selector bg-dark text-white border-0" data-part="header" onclick="highlightPart('header'); return false;" style="cursor: pointer; border-top: 1px solid rgba(255,255,255,0.1) !important;">
                                <div class="d-flex align-items-center">
                                    <span style="font-size: 20px; width: 30px;">H</span>
                                    <div>
                                        <div class="font-weight-bold">Header</div>
                                        <small class="text-white-50">Judul dan nomor surat</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action part-selector bg-dark text-white border-0" data-part="body" onclick="highlightPart('body'); return false;" style="cursor: pointer; border-top: 1px solid rgba(255,255,255,0.1) !important;">
                                <div class="d-flex align-items-center">
                                    <span style="font-size: 20px; width: 30px;">☰</span>
                                    <div>
                                        <div class="font-weight-bold">Body</div>
                                        <small class="text-white-50">Isi utama dokumen</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action part-selector bg-dark text-white border-0" data-part="footer" onclick="highlightPart('footer'); return false;" style="cursor: pointer; border-top: 1px solid rgba(255,255,255,0.1) !important;">
                                <div class="d-flex align-items-center">
                                    <span style="font-size: 20px; width: 30px;">👤</span>
                                    <div>
                                        <div class="font-weight-bold">Footer</div>
                                        <small class="text-white-50">Tanda tangan dan catatan</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-3" role="alert">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tip Admin:</strong><br>
                    <small>
                        - Pilih bagian untuk melihat struktur<br>
                        - Upload gambar kop dan tandatangan<br>
                        - Konten text akan diisi user nanti<br>
                        - Bagian bisa ditambah/dihapus sesuai kebutuhan
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save & Publish Actions -->
<div class="card">
    <div class="card-header bg-success">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-check-circle me-2"></i>Simpan & Publikasikan
        </h6>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <strong>Catatan:</strong> Setelah menyelesaikan pengaturan layout dan mengunggah gambar, klik tombol di bawah untuk menyimpan atau mempublikasikan dokumen.
        </p>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="saveDraft()">
                <i class="fas fa-save me-2"></i>Simpan Draft
            </button>
            <button type="button" class="btn btn-success" onclick="publishDocument()">
                <i class="fas fa-paper-plane me-2"></i>Publikasikan
            </button>
        </div>
    </div>
</div>

@else
    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Belum ada dokumen!</strong> <a href="{{ route('documents.input') }}">Buat dokumen baru</a>
    </div>
@endif

<script>
function chooseDocument() {
    const select = document.getElementById('document_select');
    if(select.value) {
        // Hanya tampilkan settings section
        document.getElementById('settingsSection').style.display = 'block';
        highlightPart('kop');
        // Scroll ke settings
        document.getElementById('settingsSection').scrollIntoView({ behavior: 'smooth' });
    } else {
        alert('Pilih dokumen terlebih dahulu');
    }
}

function updatePreview() {
    // Get all layout customization values
    const kopPosition = document.getElementById('kopPosition')?.value || 'top';
    const kopAlignment = document.getElementById('kopAlignment')?.value || 'center';
    const kopHeight = document.getElementById('kopHeight')?.value || '100';
    const kopSpacing = document.getElementById('kopSpacing')?.value || '20';
    const kopImageWidth = document.getElementById('kopImageWidth')?.value || '60';
    const kopImageHeight = document.getElementById('kopImageHeight')?.value || '80';
    
    const headerAlignment = document.getElementById('headerAlignment')?.value || 'center';
    const headerHeight = document.getElementById('headerHeight')?.value || '70';
    const headerFontSize = document.getElementById('headerFontSize')?.value || '14px';
    
    const bodyAlignment = document.getElementById('bodyAlignment')?.value || 'left';
    const bodyHeight = document.getElementById('bodyHeight')?.value || '200';
    const bodyLineHeight = document.getElementById('bodyLineHeight')?.value || '1.6';
    
    const signatureLayout = document.getElementById('signatureLayout')?.value || '2col';
    const footerHeight = document.getElementById('footerHeight')?.value || '150';
    const footerAlignment = document.getElementById('footerAlignment')?.value || 'center';
    
    // Update value displays for sliders
    document.getElementById('kopHeightValue').textContent = kopHeight;
    document.getElementById('kopSpacingValue').textContent = kopSpacing;
    document.getElementById('kopImageWidthValue').textContent = kopImageWidth;
    document.getElementById('kopImageHeightValue').textContent = kopImageHeight;
    
    // Update Kop Section
    const kopSection = document.querySelector('.kop-section');
    if(kopSection) {
        kopSection.style.minHeight = kopHeight + 'px';
        kopSection.style.marginBottom = kopSpacing + 'px';
        kopSection.style.textAlign = kopAlignment;
        
        // Update image sizes in preview
        const kopImages = kopSection.querySelectorAll('img');
        kopImages.forEach(img => {
            img.style.maxWidth = kopImageWidth + 'px';
            img.style.maxHeight = kopImageHeight + 'px';
        });
        
        if(kopPosition === 'bottom') {
            kopSection.style.order = '4';
            document.querySelector('.header-section').style.order = '1';
            document.querySelector('.body-section').style.order = '2';
            document.querySelector('.footer-section').style.order = '3';
            document.getElementById('layoutPreview').style.display = 'flex';
            document.getElementById('layoutPreview').style.flexDirection = 'column';
        } else {
            kopSection.style.order = '0';
            document.querySelector('.header-section').style.order = '1';
            document.querySelector('.body-section').style.order = '2';
            document.querySelector('.footer-section').style.order = '3';
        }
    }
    
    // Update Header Section
    const headerSection = document.querySelector('.header-section');
    if(headerSection) {
        headerSection.style.minHeight = headerHeight + 'px';
        headerSection.style.fontSize = headerFontSize;
        
        // Handle alignment - change from flex to block for text-align
        if(headerAlignment !== 'center') {
            headerSection.style.display = 'block';
            headerSection.style.textAlign = headerAlignment;
            headerSection.style.paddingTop = '10px';
        } else {
            headerSection.style.display = 'flex';
            headerSection.style.justifyContent = 'center';
            headerSection.style.alignItems = 'center';
        }
    }
    
    // Update Body Section
    const bodySection = document.querySelector('.body-section');
    if(bodySection) {
        bodySection.style.minHeight = bodyHeight + 'px';
        bodySection.style.textAlign = bodyAlignment;
        bodySection.style.lineHeight = bodyLineHeight;
    }
    
    // Update Footer/Signature Section
    const footerSection = document.querySelector('.footer-section');
    const signatureDisplay = document.getElementById('signatureDisplay');
    if(footerSection) {
        footerSection.style.minHeight = footerHeight + 'px';
        footerSection.style.textAlign = footerAlignment;
    }
    
    if(signatureDisplay) {
        const gridMap = {
            '2col': 'grid-template-columns: 1fr 1fr;',
            '3col': 'grid-template-columns: 1fr 1fr 1fr;',
            '4col': 'grid-template-columns: 1fr 1fr 1fr 1fr;',
            'stack': 'grid-template-columns: 1fr;'
        };
        signatureDisplay.style.cssText = `display: grid; ${gridMap[signatureLayout]} gap: 20px; margin-bottom: 20px;`;
    }
}


function previewImage(input, previewId, textId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            const textEl = document.getElementById(textId);
            const kopImageWidth = document.getElementById('kopImageWidth')?.value || '60';
            const kopImageHeight = document.getElementById('kopImageHeight')?.value || '80';
            
            preview.innerHTML = '<img src="' + e.target.result + '" class="preview-img">';
            textEl.style.display = 'none';
            
            // Update preview jika ada di layout
            if(previewId === 'kopLeftPreview') {
                document.getElementById('kopLeftDisplay').innerHTML = '<img src="' + e.target.result + '" class="preview-img" style="max-width: ' + kopImageWidth + 'px; max-height: ' + kopImageHeight + 'px;">';
            } else if(previewId === 'kopRightPreview') {
                document.getElementById('kopRightDisplay').innerHTML = '<img src="' + e.target.result + '" class="preview-img" style="max-width: ' + kopImageWidth + 'px; max-height: ' + kopImageHeight + 'px;">';
            } else if(previewId === 'signaturePreview') {
                // Update both signature displays
                document.querySelectorAll('#signatureDisplay > div').forEach((el, idx) => {
                    if(idx < 2) {
                        el.innerHTML = '<div style="border: 2px dashed #ccc; padding: 20px; background: #fff8f0; border-radius: 4px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;"><img src="' + e.target.result + '" class="preview-img" style="max-height: 80px;"></div><p style="margin: 0; font-size: 9px;">&lt;&lt;Nama&gt;&gt;</p>';
                    }
                });
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function highlightPart(part) {
    // Reset all parts
    const sections = ['kop-section', 'header-section', 'body-section', 'footer-section'];
    sections.forEach(section => {
        const el = document.querySelector('.' + section);
        if(el) {
            el.style.borderColor = '#999';
            el.style.backgroundColor = '#fff';
        }
    });
    
    // Remove active class from all selectors
    document.querySelectorAll('.part-selector').forEach(el => {
        el.classList.remove('active');
        el.style.backgroundColor = 'inherit';
        el.style.color = 'white';
    });
    
    // Highlight selected part
    const partMap = {
        'kop': 'kop-section',
        'header': 'header-section',
        'body': 'body-section',
        'footer': 'footer-section'
    };
    
    if(partMap[part]) {
        const el = document.querySelector('.' + partMap[part]);
        if(el) {
            el.style.borderColor = '#0d6efd';
            el.style.backgroundColor = '#e7f3ff';
        }
    }
    
    // Set active class on selector
    const selector = document.querySelector(`[data-part="${part}"]`);
    if(selector) {
        selector.classList.add('active');
        selector.style.backgroundColor = 'rgba(13, 110, 253, 0.2)';
    }
}

function addNewPart() {
    const partName = prompt('Masukkan nama bagian custom (misal: logo, pengesahan, lampiran, dll):');
    if (!partName) return;
    
    alert('✓ Bagian custom "' + partName + '" akan ditambahkan ke dokumen.\n\nPerubahan akan disimpan saat halaman user mengakses dokumen ini.');
}

function loadDocumentSettings() {
    const docId = document.getElementById('document_select').value;
    if (docId) {
        document.getElementById('settingsSection').style.display = 'block';
        highlightPart('kop');
        
        // Fetch existing layout settings from server
        fetch(`/documents/${docId}/editor`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Fetched document data:', data);
            console.log('Layout settings:', data.layout_settings);
            
            if (data.layout_settings) {
                const settings = data.layout_settings;
                
                // Populate all form fields with saved settings
                if (settings.kop_position) document.getElementById('kopPosition').value = settings.kop_position;
                if (settings.kop_alignment) document.getElementById('kopAlignment').value = settings.kop_alignment;
                if (settings.kop_height) {
                    document.getElementById('kopHeight').value = settings.kop_height;
                    document.getElementById('kopHeightValue').textContent = settings.kop_height;
                }
                if (settings.kop_spacing) {
                    document.getElementById('kopSpacing').value = settings.kop_spacing;
                    document.getElementById('kopSpacingValue').textContent = settings.kop_spacing;
                }
                if (settings.kop_image_width) {
                    document.getElementById('kopImageWidth').value = settings.kop_image_width;
                    document.getElementById('kopImageWidthValue').textContent = settings.kop_image_width;
                }
                if (settings.kop_image_height) {
                    document.getElementById('kopImageHeight').value = settings.kop_image_height;
                    document.getElementById('kopImageHeightValue').textContent = settings.kop_image_height;
                }
                
                if (settings.document_font_family) document.getElementById('documentFontFamily').value = settings.document_font_family;
                if (settings.document_margin) document.getElementById('documentMargin').value = settings.document_margin;
                
                if (settings.header_alignment) {
                    console.log('Setting header alignment to:', settings.header_alignment);
                    document.getElementById('headerAlignment').value = settings.header_alignment;
                }
                if (settings.header_height) document.getElementById('headerHeight').value = settings.header_height;
                if (settings.header_font_size) document.getElementById('headerFontSize').value = settings.header_font_size;
                
                if (settings.body_alignment) document.getElementById('bodyAlignment').value = settings.body_alignment;
                if (settings.body_height) document.getElementById('bodyHeight').value = settings.body_height;
                if (settings.body_line_height) document.getElementById('bodyLineHeight').value = settings.body_line_height;
                
                if (settings.footer_layout) document.getElementById('signatureLayout').value = settings.footer_layout;
                if (settings.footer_height) document.getElementById('footerHeight').value = settings.footer_height;
                if (settings.footer_alignment) document.getElementById('footerAlignment').value = settings.footer_alignment;
                if (settings.signature_width) {
                    document.getElementById('signatureWidth').value = settings.signature_width;
                    document.getElementById('signatureWidthValue').textContent = settings.signature_width;
                }
                
                console.log('Calling updatePreview()');
                // Update preview setelah load settings
                updatePreview();
            } else {
                console.log('No layout_settings found, calling updatePreview with defaults');
                updatePreview();
            }
        })
        .catch(error => console.error('Error loading document settings:', error));
    } else {
        document.getElementById('settingsSection').style.display = 'none';
    }
}

function saveDraft() {
    const docId = document.getElementById('document_select').value;
    if (!docId) {
        alert('Pilih dokumen terlebih dahulu');
        return false;
    }
    
    // Save layout settings
    saveLayoutSettings();
    alert('Draft saved! Dokumentasi telah disimpan sebagai draft.');
}

function saveLayoutSettings() {
    const docId = document.getElementById('document_select').value;
    if (!docId) {
        console.error('No document selected');
        return;
    }

    const layoutSettings = {
        document_font_family: document.getElementById('documentFontFamily')?.value || 'Times New Roman',
        document_margin: document.getElementById('documentMargin')?.value || 20,
        kop_position: document.getElementById('kopPosition')?.value || 'top',
        kop_alignment: document.getElementById('kopAlignment')?.value || 'center',
        kop_height: document.getElementById('kopHeight')?.value || 100,
        kop_spacing: document.getElementById('kopSpacing')?.value || 20,
        kop_image_width: document.getElementById('kopImageWidth')?.value || 60,
        kop_image_height: document.getElementById('kopImageHeight')?.value || 80,
        header_alignment: document.getElementById('headerAlignment')?.value || 'center',
        header_height: document.getElementById('headerHeight')?.value || 70,
        header_font_size: document.getElementById('headerFontSize')?.value || '14px',
        body_alignment: document.getElementById('bodyAlignment')?.value || 'left',
        body_height: document.getElementById('bodyHeight')?.value || 200,
        body_line_height: document.getElementById('bodyLineHeight')?.value || '1.6',
        footer_layout: document.getElementById('signatureLayout')?.value || '2col',
        footer_height: document.getElementById('footerHeight')?.value || 150,
        footer_alignment: document.getElementById('footerAlignment')?.value || 'center',
        signature_width: document.getElementById('signatureWidth')?.value || 80
    };

    console.log('Saving layout settings:', layoutSettings);
    console.log('Sending to URL: /documents/' + docId + '/save-layout');

    // Send to server via AJAX
    fetch(`/documents/${docId}/save-layout`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(layoutSettings)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data);
        if (data.success) {
            console.log('Layout settings saved successfully');
            alert('✓ Layout Settings berhasil disimpan!');
            updatePreview();
        } else {
            alert('✗ Gagal menyimpan layout settings');
        }
    })
    .catch(error => {
        console.error('Error saving layout:', error);
        alert('✗ Terjadi kesalahan saat menyimpan layout settings: ' + error.message);
    });
}

function publishDocument() {
    const docId = document.getElementById('document_select').value;
    if (!docId) {
        alert('Pilih dokumen terlebih dahulu');
        return false;
    }
    
    if (confirm('Apakah Anda yakin ingin mempublikasikan dokumen ini? User akan bisa mengakses dokumen setelah ini.')) {
        // Submit form to publish endpoint
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/documents/' + docId + '/publish';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
    return false;
}

// Set default highlight on page load
document.addEventListener('DOMContentLoaded', function() {
    highlightPart('kop');
    
    // Update slider value displays
    const sliders = [
        { id: 'kopHeight', display: 'kopHeightValue' },
        { id: 'kopSpacing', display: 'kopSpacingValue' },
        { id: 'headerHeight', display: 'headerHeightValue' },
        { id: 'bodyHeight', display: 'bodyHeightValue' },
        { id: 'footerHeight', display: 'footerHeightValue' }
    ];
    
    sliders.forEach(slider => {
        const el = document.getElementById(slider.id);
        const displayEl = document.getElementById(slider.display);
        if(el && displayEl) {
            el.addEventListener('input', function() {
                displayEl.textContent = this.value;
                updatePreview();
            });
        }
    });
});
</script>

<style>
    .preview-img {
        max-width: 100%;
        max-height: 140px;
        object-fit: contain;
        border-radius: 4px;
    }
    
    .part-selector.active {
        background-color: rgba(13, 110, 253, 0.2) !important;
        border-left: 4px solid #0d6efd;
        padding-left: calc(1rem - 4px) !important;
    }
</style>

</div> <!-- Close settingsSection -->

@endsection

