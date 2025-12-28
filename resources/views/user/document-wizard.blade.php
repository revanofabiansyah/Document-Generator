@extends('layouts.app')

@section('content')

<section class="py-4">
    <div class="container px-5">
        <!-- Header dengan Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 fw-bolder mb-1">{{ $document->title }}</h1>
                <p class="text-muted mb-0">Langkah {{ $currentStep ?? 1 }} dari 5</p>
            </div>
            <a href="{{ route('documents.user.list', ['user' => $user->name]) }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <!-- Step Indicator -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center">
                @php
                    $steps = [
                        ['number' => 1, 'name' => 'Kop Surat', 'id' => 'step-kop'],
                        ['number' => 2, 'name' => 'Header', 'id' => 'step-header'],
                        ['number' => 3, 'name' => 'Body', 'id' => 'step-body'],
                        ['number' => 4, 'name' => 'Footer', 'id' => 'step-footer'],
                        ['number' => 5, 'name' => 'Review', 'id' => 'step-review']
                    ];
                    $currentStep = $currentStep ?? 1;
                @endphp
                @foreach($steps as $step)
                    <div class="text-center" style="flex: 1;">
                        <button type="button" 
                                class="btn-step"
                                style="width: 40px; height: 40px; border-radius: 50%; background-color: {{ $currentStep >= $step['number'] ? '#0d6efd' : '#e9ecef' }}; color: white; font-weight: bold; cursor: pointer; border: none;"
                                onclick="goToStep({{ $step['number'] }})">
                            {{ $step['number'] }}
                        </button>
                        <div style="font-size: 12px; margin-top: 8px;">{{ $step['name'] }}</div>
                    </div>
                    @if($loop->index < count($steps) - 1)
                        <div style="flex: 1; height: 2px; background-color: {{ $currentStep > $step['number'] ? '#0d6efd' : '#e9ecef' }}; margin-bottom: 20px;"></div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="d-flex gap-4" style="flex-wrap: nowrap;">
            <!-- Left Side: Form Input -->
            <div style="flex: 0 0 45%; min-width: 0;">
            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="fw-bold mb-3">Oops! Ada Kesalahan:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <form id="documentForm" method="POST" action="{{ route('documents.user.save', ['user' => $user->name, 'document' => $document]) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="current_step" id="current_step_input" value="{{ $currentStep }}">
                <input type="hidden" id="documentTitle" value="{{ $document->title }}">

                <!-- Step 1: Kop Surat -->
                <div class="step-section" id="step-1" style="{{ $currentStep == 1 ? '' : 'display: none;' }}">
                    <h5 class="fw-bold mb-4">Kop Surat</h5>
                    <p class="text-muted mb-4"><small>Isi data instansi/perusahaan dan custom ukuran font setiap field</small></p>
                    
                    @php
                        // Check if granular kop parts exist (new structure) or old generic kop part exists
                        $hasKopParts = $document->parts->where('part_name', 'kop_instansi')->count() > 0 || 
                                      $document->parts->where('part_name', 'kop')->count() > 0;
                    @endphp

                    @if($hasKopParts)
                        <!-- Instansi Field -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0"><strong><i class="bi bi-building me-2"></i>Instansi / Perusahaan</strong></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="kop_instansi" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="kop_instansi" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="kop_instansi" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="kop_instansi_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['kop_instansi_font_size'] ?? '16px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="12px" {{ ($documentParts['kop_instansi_font_size'] ?? '16px') == '12px' ? 'selected' : '' }}>12px</option>
                                        <option value="14px" {{ ($documentParts['kop_instansi_font_size'] ?? '16px') == '14px' ? 'selected' : '' }}>14px</option>
                                        <option value="16px" {{ ($documentParts['kop_instansi_font_size'] ?? '16px') == '16px' ? 'selected' : '' }}>16px</option>
                                        <option value="18px" {{ ($documentParts['kop_instansi_font_size'] ?? '16px') == '18px' ? 'selected' : '' }}>18px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="kop_instansi_bold" value="{{ $documentParts['kop_instansi_bold'] ?? '0' }}">
                            <input type="hidden" name="kop_instansi_italic" value="{{ $documentParts['kop_instansi_italic'] ?? '0' }}">
                            <input type="hidden" name="kop_instansi_underline" value="{{ $documentParts['kop_instansi_underline'] ?? '0' }}">
                            <textarea name="kop_instansi" class="form-control form-input-preview" rows="2" placeholder="Contoh: PEMERINTAH KOTA BANDUNG">{{ $documentParts['kop_instansi'] ?? '' }}</textarea>
                        </div>

                        <!-- Nama Instansi Field -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0"><strong><i class="bi bi-tag me-2"></i>Nama Instansi / Cabang / Dinas</strong></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="kop_nama" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="kop_nama" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="kop_nama" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="kop_nama_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['kop_nama_font_size'] ?? '18px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="12px" {{ ($documentParts['kop_nama_font_size'] ?? '18px') == '12px' ? 'selected' : '' }}>12px</option>
                                        <option value="14px" {{ ($documentParts['kop_nama_font_size'] ?? '18px') == '14px' ? 'selected' : '' }}>14px</option>
                                        <option value="16px" {{ ($documentParts['kop_nama_font_size'] ?? '18px') == '16px' ? 'selected' : '' }}>16px</option>
                                        <option value="18px" {{ ($documentParts['kop_nama_font_size'] ?? '18px') == '18px' ? 'selected' : '' }}>18px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="kop_nama_bold" value="{{ $documentParts['kop_nama_bold'] ?? '0' }}">
                            <input type="hidden" name="kop_nama_italic" value="{{ $documentParts['kop_nama_italic'] ?? '0' }}">
                            <input type="hidden" name="kop_nama_underline" value="{{ $documentParts['kop_nama_underline'] ?? '0' }}">
                            <textarea name="kop_nama" class="form-control form-input-preview" rows="2" placeholder="Contoh: DINAS PENDIDIKAN">{{ $documentParts['kop_nama'] ?? '' }}</textarea>
                        </div>

                        <!-- Alamat Field -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0"><strong><i class="bi bi-geo-alt me-2"></i>Jalan / Alamat</strong></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="kop_alamat" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="kop_alamat" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="kop_alamat" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="kop_alamat_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="8px" {{ ($documentParts['kop_alamat_font_size'] ?? '12px') == '8px' ? 'selected' : '' }}>8px</option>
                                        <option value="9px" {{ ($documentParts['kop_alamat_font_size'] ?? '12px') == '9px' ? 'selected' : '' }}>9px</option>
                                        <option value="10px" {{ ($documentParts['kop_alamat_font_size'] ?? '12px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="11px" {{ ($documentParts['kop_alamat_font_size'] ?? '12px') == '11px' ? 'selected' : '' }}>11px</option>
                                        <option value="12px" {{ ($documentParts['kop_alamat_font_size'] ?? '12px') == '12px' ? 'selected' : '' }}>12px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="kop_alamat_bold" value="{{ $documentParts['kop_alamat_bold'] ?? '0' }}">
                            <input type="hidden" name="kop_alamat_italic" value="{{ $documentParts['kop_alamat_italic'] ?? '0' }}">
                            <input type="hidden" name="kop_alamat_underline" value="{{ $documentParts['kop_alamat_underline'] ?? '0' }}">
                            <textarea name="kop_alamat" class="form-control form-input-preview" rows="2" placeholder="Contoh: Jalan Perjuangan No. 123">{{ $documentParts['kop_alamat'] ?? '' }}</textarea>
                        </div>

                        <!-- Kontak Fields -->
                        <div class="mb-4">
                            <label class="form-label mb-2"><strong><i class="bi bi-telephone me-2"></i>Kontak</strong></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div class="d-flex gap-2 align-items-center mb-2">
                                            <span style="font-size: 12px; color: #666; min-width: 40px;">Telp:</span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="kop_telp" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="kop_telp" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="kop_telp" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                            <select name="kop_telp_font_size" class="form-select form-select-sm form-input-preview" style="min-width: 70px;" onchange="updateLivePreview()">
                                                <option value="8px" {{ ($documentParts['kop_telp_font_size'] ?? '12px') == '8px' ? 'selected' : '' }}>8px</option>
                                                <option value="9px" {{ ($documentParts['kop_telp_font_size'] ?? '12px') == '9px' ? 'selected' : '' }}>9px</option>
                                                <option value="10px" {{ ($documentParts['kop_telp_font_size'] ?? '12px') == '10px' ? 'selected' : '' }}>10px</option>
                                                <option value="11px" {{ ($documentParts['kop_telp_font_size'] ?? '12px') == '11px' ? 'selected' : '' }}>11px</option>
                                                <option value="12px" {{ ($documentParts['kop_telp_font_size'] ?? '12px') == '12px' ? 'selected' : '' }}>12px</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="kop_telp_bold" value="{{ $documentParts['kop_telp_bold'] ?? '0' }}">
                                        <input type="hidden" name="kop_telp_italic" value="{{ $documentParts['kop_telp_italic'] ?? '0' }}">
                                        <input type="hidden" name="kop_telp_underline" value="{{ $documentParts['kop_telp_underline'] ?? '0' }}">
                                        <input type="text" name="kop_telp" class="form-control form-control-sm form-input-preview" placeholder="Telp" value="{{ $documentParts['kop_telp'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div class="d-flex gap-2 align-items-center mb-2">
                                            <span style="font-size: 12px; color: #666; min-width: 50px;">Email:</span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="kop_email" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="kop_email" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="kop_email" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                            <select name="kop_email_font_size" class="form-select form-select-sm form-input-preview" style="min-width: 70px;" onchange="updateLivePreview()">
                                                <option value="8px" {{ ($documentParts['kop_email_font_size'] ?? '12px') == '8px' ? 'selected' : '' }}>8px</option>
                                                <option value="9px" {{ ($documentParts['kop_email_font_size'] ?? '12px') == '9px' ? 'selected' : '' }}>9px</option>
                                                <option value="10px" {{ ($documentParts['kop_email_font_size'] ?? '12px') == '10px' ? 'selected' : '' }}>10px</option>
                                                <option value="11px" {{ ($documentParts['kop_email_font_size'] ?? '12px') == '11px' ? 'selected' : '' }}>11px</option>
                                                <option value="12px" {{ ($documentParts['kop_email_font_size'] ?? '12px') == '12px' ? 'selected' : '' }}>12px</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="kop_email_bold" value="{{ $documentParts['kop_email_bold'] ?? '0' }}">
                                        <input type="hidden" name="kop_email_italic" value="{{ $documentParts['kop_email_italic'] ?? '0' }}">
                                        <input type="hidden" name="kop_email_underline" value="{{ $documentParts['kop_email_underline'] ?? '0' }}">
                                        <input type="email" name="kop_email" class="form-control form-control-sm form-input-preview" placeholder="Email" value="{{ $documentParts['kop_email'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Uploads -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong><i class="bi bi-image me-2"></i>Foto Kiri</strong></label>
                                <div class="upload-box mb-2">
                                    <input type="file" name="kop_left" id="kop_left_input" accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('kop_left_input').click()">
                                        <i class="bi bi-folder2-open me-1"></i>Choose File
                                    </button>
                                    <span id="kop_left_name" class="ms-2 text-muted small">No file chosen</span>
                                </div>
                                <small class="text-muted">JPG/JPEG/PNG Format</small>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="kop_left_save" style="display: none;" onclick="saveImage('kop_left')">
                                        <i class="bi bi-check-circle me-1"></i>Save Foto
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="kop_left_delete" style="display: none;" onclick="deleteImage('kop_left')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><strong><i class="bi bi-image me-2"></i>Foto Kantor</strong></label>
                                <div class="upload-box mb-2">
                                    <input type="file" name="kop_right" id="kop_right_input" accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('kop_right_input').click()">
                                        <i class="bi bi-folder2-open me-1"></i>Choose File
                                    </button>
                                    <span id="kop_right_name" class="ms-2 text-muted small">No file chosen</span>
                                </div>
                                <small class="text-muted">JPG/JPEG/PNG Format</small>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="kop_right_save" style="display: none;" onclick="saveImage('kop_right')">
                                        <i class="bi bi-check-circle me-1"></i>Save Foto
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="kop_right_delete" style="display: none;" onclick="deleteImage('kop_right')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Bagian Kop Surat belum di-setup oleh admin. Hubungi admin untuk konfigurasi template.
                        </div>
                    @endif
                </div>

                <!-- Step 2: Header -->
                <div class="step-section" id="step-2" style="{{ $currentStep == 2 ? '' : 'display: none;' }}">
                    <h5 class="fw-bold mb-4">Header</h5>

                    <!-- Tempat & Tanggal -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0"><strong>Tempat & Tanggal</strong></label>
                            <div class="d-flex gap-2 align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="header_tempat_tanggal" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="header_tempat_tanggal" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="header_tempat_tanggal" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                <select name="header_tempat_tanggal_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                    <option value="10px" {{ ($documentParts['header_tempat_tanggal_font_size'] ?? '12px') == '10px' ? 'selected' : '' }}>10px</option>
                                    <option value="11px" {{ ($documentParts['header_tempat_tanggal_font_size'] ?? '12px') == '11px' ? 'selected' : '' }}>11px</option>
                                    <option value="12px" {{ ($documentParts['header_tempat_tanggal_font_size'] ?? '12px') == '12px' ? 'selected' : '' }}>12px</option>
                                    <option value="13px" {{ ($documentParts['header_tempat_tanggal_font_size'] ?? '12px') == '13px' ? 'selected' : '' }}>13px</option>
                                    <option value="14px" {{ ($documentParts['header_tempat_tanggal_font_size'] ?? '12px') == '14px' ? 'selected' : '' }}>14px</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="header_tempat_tanggal_bold" value="{{ $documentParts['header_tempat_tanggal_bold'] ?? '0' }}">
                        <input type="hidden" name="header_tempat_tanggal_italic" value="{{ $documentParts['header_tempat_tanggal_italic'] ?? '0' }}">
                        <input type="hidden" name="header_tempat_tanggal_underline" value="{{ $documentParts['header_tempat_tanggal_underline'] ?? '0' }}">
                        <input type="text" name="header_tempat_tanggal" class="form-control form-input-preview" value="{{ $documentParts['header_tempat_tanggal'] ?? '' }}">
                    </div>

                    <!-- Nomor -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0"><strong>Nomor</strong></label>
                            <div class="d-flex gap-2 align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="header_nomor" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="header_nomor" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="header_nomor" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                <select name="header_nomor_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                    <option value="10px" {{ ($documentParts['header_nomor_font_size'] ?? '11px') == '10px' ? 'selected' : '' }}>10px</option>
                                    <option value="11px" {{ ($documentParts['header_nomor_font_size'] ?? '11px') == '11px' ? 'selected' : '' }}>11px</option>
                                    <option value="12px" {{ ($documentParts['header_nomor_font_size'] ?? '11px') == '12px' ? 'selected' : '' }}>12px</option>
                                    <option value="13px" {{ ($documentParts['header_nomor_font_size'] ?? '11px') == '13px' ? 'selected' : '' }}>13px</option>
                                    <option value="14px" {{ ($documentParts['header_nomor_font_size'] ?? '11px') == '14px' ? 'selected' : '' }}>14px</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="header_nomor_bold" value="{{ $documentParts['header_nomor_bold'] ?? '0' }}">
                        <input type="hidden" name="header_nomor_italic" value="{{ $documentParts['header_nomor_italic'] ?? '0' }}">
                        <input type="hidden" name="header_nomor_underline" value="{{ $documentParts['header_nomor_underline'] ?? '0' }}">
                        <input type="text" name="header_nomor" class="form-control form-input-preview" value="{{ $documentParts['header_nomor'] ?? '' }}">
                    </div>

                    <!-- Lampiran & Perihal -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0"><strong>Lampiran</strong></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="header_lampiran" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="header_lampiran" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="header_lampiran" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="header_lampiran_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['header_lampiran_font_size'] ?? '11px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="11px" {{ ($documentParts['header_lampiran_font_size'] ?? '11px') == '11px' ? 'selected' : '' }}>11px</option>
                                        <option value="12px" {{ ($documentParts['header_lampiran_font_size'] ?? '11px') == '12px' ? 'selected' : '' }}>12px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="header_lampiran_bold" value="{{ $documentParts['header_lampiran_bold'] ?? '0' }}">
                            <input type="hidden" name="header_lampiran_italic" value="{{ $documentParts['header_lampiran_italic'] ?? '0' }}">
                            <input type="hidden" name="header_lampiran_underline" value="{{ $documentParts['header_lampiran_underline'] ?? '0' }}">
                            <input type="text" name="header_lampiran" class="form-control form-input-preview" value="{{ $documentParts['header_lampiran'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0"><strong>Perihal</strong></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="header_perihal" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="header_perihal" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="header_perihal" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="header_perihal_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['header_perihal_font_size'] ?? '11px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="11px" {{ ($documentParts['header_perihal_font_size'] ?? '11px') == '11px' ? 'selected' : '' }}>11px</option>
                                        <option value="12px" {{ ($documentParts['header_perihal_font_size'] ?? '11px') == '12px' ? 'selected' : '' }}>12px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="header_perihal_bold" value="{{ $documentParts['header_perihal_bold'] ?? '0' }}">
                            <input type="hidden" name="header_perihal_italic" value="{{ $documentParts['header_perihal_italic'] ?? '0' }}">
                            <input type="hidden" name="header_perihal_underline" value="{{ $documentParts['header_perihal_underline'] ?? '0' }}">
                            <input type="text" name="header_perihal" class="form-control form-input-preview" value="{{ $documentParts['header_perihal'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Body Header -->
                    <div class="mb-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0"><strong>Body Header</strong></label>
                            <div class="d-flex gap-2 align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="header_body" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="header_body" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="header_body" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                <select name="header_body_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                    <option value="10px" {{ ($documentParts['header_body_font_size'] ?? '11px') == '10px' ? 'selected' : '' }}>10px</option>
                                    <option value="11px" {{ ($documentParts['header_body_font_size'] ?? '11px') == '11px' ? 'selected' : '' }}>11px</option>
                                    <option value="12px" {{ ($documentParts['header_body_font_size'] ?? '11px') == '12px' ? 'selected' : '' }}>12px</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="header_body_bold" value="{{ $documentParts['header_body_bold'] ?? '0' }}">
                        <input type="hidden" name="header_body_italic" value="{{ $documentParts['header_body_italic'] ?? '0' }}">
                        <input type="hidden" name="header_body_underline" value="{{ $documentParts['header_body_underline'] ?? '0' }}">
                        <textarea name="header_body" class="form-control form-input-preview" rows="4">{{ $documentParts['header_body'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Step 3: Body -->
                <div class="step-section" id="step-3" style="{{ $currentStep == 3 ? '' : 'display: none;' }}">
                    <h5 class="fw-bold mb-4">Body</h5>

                    <!-- First Paragraph -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0"><strong>First Paragraph</strong></label>
                            <div class="d-flex gap-2 align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="body_paragraph1" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="body_paragraph1" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="body_paragraph1" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                <select name="body_paragraph1_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                    <option value="10px" {{ ($documentParts['body_paragraph1_font_size'] ?? '10px') == '10px' ? 'selected' : '' }}>10px</option>
                                    <option value="11px" {{ ($documentParts['body_paragraph1_font_size'] ?? '10px') == '11px' ? 'selected' : '' }}>11px</option>
                                    <option value="12px" {{ ($documentParts['body_paragraph1_font_size'] ?? '10px') == '12px' ? 'selected' : '' }}>12px</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="body_paragraph1_bold" value="{{ $documentParts['body_paragraph1_bold'] ?? '0' }}">
                        <input type="hidden" name="body_paragraph1_italic" value="{{ $documentParts['body_paragraph1_italic'] ?? '0' }}">
                        <input type="hidden" name="body_paragraph1_underline" value="{{ $documentParts['body_paragraph1_underline'] ?? '0' }}">
                        <textarea name="body_paragraph1" class="form-control form-input-preview" rows="4" onchange="updateLivePreview()">{{ $documentParts['body_paragraph1'] ?? '' }}</textarea>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Keterangan</strong></label>
                        
                        <!-- Hari + Font Size -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small mb-0">Hari</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="body_hari" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="body_hari" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="body_hari" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="body_hari_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['body_hari_font_size'] ?? '10px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="11px" {{ ($documentParts['body_hari_font_size'] ?? '10px') == '11px' ? 'selected' : '' }}>11px</option>
                                        <option value="12px" {{ ($documentParts['body_hari_font_size'] ?? '10px') == '12px' ? 'selected' : '' }}>12px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="body_hari_bold" value="{{ $documentParts['body_hari_bold'] ?? '0' }}">
                            <input type="hidden" name="body_hari_italic" value="{{ $documentParts['body_hari_italic'] ?? '0' }}">
                            <input type="hidden" name="body_hari_underline" value="{{ $documentParts['body_hari_underline'] ?? '0' }}">
                            <input type="text" name="body_hari" class="form-control form-control-sm form-input-preview" value="{{ $documentParts['body_hari'] ?? '' }}" onchange="updateLivePreview()">
                        </div>

                        <!-- Tanggal + Font Size -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small mb-0">Tanggal</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="body_tanggal" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="body_tanggal" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="body_tanggal" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="body_tanggal_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['body_tanggal_font_size'] ?? '10px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="11px" {{ ($documentParts['body_tanggal_font_size'] ?? '10px') == '11px' ? 'selected' : '' }}>11px</option>
                                        <option value="12px" {{ ($documentParts['body_tanggal_font_size'] ?? '10px') == '12px' ? 'selected' : '' }}>12px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="body_tanggal_bold" value="{{ $documentParts['body_tanggal_bold'] ?? '0' }}">
                            <input type="hidden" name="body_tanggal_italic" value="{{ $documentParts['body_tanggal_italic'] ?? '0' }}">
                            <input type="hidden" name="body_tanggal_underline" value="{{ $documentParts['body_tanggal_underline'] ?? '0' }}">
                            <input type="text" name="body_tanggal" class="form-control form-control-sm form-input-preview" value="{{ $documentParts['body_tanggal'] ?? '' }}" onchange="updateLivePreview()">
                        </div>

                        <!-- Waktu + Font Size -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small mb-0">Waktu</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="body_waktu" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="body_waktu" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="body_waktu" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="body_waktu_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['body_waktu_font_size'] ?? '10px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="11px" {{ ($documentParts['body_waktu_font_size'] ?? '10px') == '11px' ? 'selected' : '' }}>11px</option>
                                        <option value="12px" {{ ($documentParts['body_waktu_font_size'] ?? '10px') == '12px' ? 'selected' : '' }}>12px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="body_waktu_bold" value="{{ $documentParts['body_waktu_bold'] ?? '0' }}">
                            <input type="hidden" name="body_waktu_italic" value="{{ $documentParts['body_waktu_italic'] ?? '0' }}">
                            <input type="hidden" name="body_waktu_underline" value="{{ $documentParts['body_waktu_underline'] ?? '0' }}">
                            <input type="text" name="body_waktu" class="form-control form-control-sm form-input-preview" value="{{ $documentParts['body_waktu'] ?? '' }}" onchange="updateLivePreview()">
                        </div>

                        <!-- Tempat + Font Size -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small mb-0">Tempat</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="body_tempat" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="body_tempat" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="body_tempat" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                    <select name="body_tempat_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                        <option value="10px" {{ ($documentParts['body_tempat_font_size'] ?? '10px') == '10px' ? 'selected' : '' }}>10px</option>
                                        <option value="11px" {{ ($documentParts['body_tempat_font_size'] ?? '10px') == '11px' ? 'selected' : '' }}>11px</option>
                                        <option value="12px" {{ ($documentParts['body_tempat_font_size'] ?? '10px') == '12px' ? 'selected' : '' }}>12px</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="body_tempat_bold" value="{{ $documentParts['body_tempat_bold'] ?? '0' }}">
                            <input type="hidden" name="body_tempat_italic" value="{{ $documentParts['body_tempat_italic'] ?? '0' }}">
                            <input type="hidden" name="body_tempat_underline" value="{{ $documentParts['body_tempat_underline'] ?? '0' }}">
                            <input type="text" name="body_tempat" class="form-control form-control-sm form-input-preview" value="{{ $documentParts['body_tempat'] ?? '' }}" onchange="updateLivePreview()">
                        </div>
                    </div>

                    <!-- Second Paragraph -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0"><strong>Second Paragraph</strong></label>
                            <div class="d-flex gap-2 align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="body_paragraph2" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="body_paragraph2" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="body_paragraph2" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                <select name="body_paragraph2_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                    <option value="10px" {{ ($documentParts['body_paragraph2_font_size'] ?? '10px') == '10px' ? 'selected' : '' }}>10px</option>
                                    <option value="11px" {{ ($documentParts['body_paragraph2_font_size'] ?? '10px') == '11px' ? 'selected' : '' }}>11px</option>
                                    <option value="12px" {{ ($documentParts['body_paragraph2_font_size'] ?? '10px') == '12px' ? 'selected' : '' }}>12px</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="body_paragraph2_bold" value="{{ $documentParts['body_paragraph2_bold'] ?? '0' }}">
                        <input type="hidden" name="body_paragraph2_italic" value="{{ $documentParts['body_paragraph2_italic'] ?? '0' }}">
                        <input type="hidden" name="body_paragraph2_underline" value="{{ $documentParts['body_paragraph2_underline'] ?? '0' }}">
                        <textarea name="body_paragraph2" class="form-control form-input-preview" rows="4" onchange="updateLivePreview()">{{ $documentParts['body_paragraph2'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Step 4: Footer -->
                <div class="step-section" id="step-4" style="{{ $currentStep == 4 ? '' : 'display: none;' }}">
                    <h5 class="fw-bold mb-4">Footer</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Jabatan Kiri -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0"><strong>Jabatan (Kiri)</strong></label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_jabatan_kiri" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_jabatan_kiri" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_jabatan_kiri" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                        <select name="footer_jabatan_kiri_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                            <option value="8px" {{ ($documentParts['footer_jabatan_kiri_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                            <option value="9px" {{ ($documentParts['footer_jabatan_kiri_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                            <option value="10px" {{ ($documentParts['footer_jabatan_kiri_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                            <option value="11px" {{ ($documentParts['footer_jabatan_kiri_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                            <option value="12px" {{ ($documentParts['footer_jabatan_kiri_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="footer_jabatan_kiri_bold" value="{{ $documentParts['footer_jabatan_kiri_bold'] ?? '0' }}">
                                <input type="hidden" name="footer_jabatan_kiri_italic" value="{{ $documentParts['footer_jabatan_kiri_italic'] ?? '0' }}">
                                <input type="hidden" name="footer_jabatan_kiri_underline" value="{{ $documentParts['footer_jabatan_kiri_underline'] ?? '0' }}">
                                <input type="text" name="footer_jabatan_kiri" class="form-control form-input-preview" value="{{ $documentParts['footer_jabatan_kiri'] ?? '' }}" oninput="updateLivePreview()">
                            </div>
                            
                            <!-- Tanda Tangan Kiri -->
                            <label class="form-label mt-3"><strong>Tanda Tangan (Kiri)</strong></label>
                            <div class="upload-box mb-2">
                                <input type="file" name="footer_signature_kiri" id="footer_signature_kiri_input" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('footer_signature_kiri_input').click()">
                                    <i class="bi bi-folder2-open me-1"></i>Choose File
                                </button>
                                <span id="footer_signature_kiri_name" class="ms-2 text-muted small">No file chosen</span>
                            </div>
                            <small class="text-muted">JPG/JPEG/PNG Format</small>
                            <div class="mt-2" style="display: flex; gap: 10px;">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="footer_signature_kiri_save" style="display: none;" onclick="saveImage('footer_signature_kiri')">
                                    <i class="bi bi-check-circle me-1"></i>Save
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="footer_signature_kiri_delete" style="display: none;" onclick="deleteImage('footer_signature_kiri')">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </div>

                            <!-- Nama Kiri -->
                            <div class="mb-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0"><strong>Nama (Kiri)</strong></label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_nama_kiri" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_nama_kiri" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_nama_kiri" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                        <select name="footer_nama_kiri_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                            <option value="8px" {{ ($documentParts['footer_nama_kiri_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                            <option value="9px" {{ ($documentParts['footer_nama_kiri_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                            <option value="10px" {{ ($documentParts['footer_nama_kiri_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                            <option value="11px" {{ ($documentParts['footer_nama_kiri_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                            <option value="12px" {{ ($documentParts['footer_nama_kiri_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="footer_nama_kiri_bold" value="{{ $documentParts['footer_nama_kiri_bold'] ?? '0' }}">
                                <input type="hidden" name="footer_nama_kiri_italic" value="{{ $documentParts['footer_nama_kiri_italic'] ?? '0' }}">
                                <input type="hidden" name="footer_nama_kiri_underline" value="{{ $documentParts['footer_nama_kiri_underline'] ?? '0' }}">
                                <input type="text" name="footer_nama_kiri" class="form-control form-input-preview" value="{{ $documentParts['footer_nama_kiri'] ?? '' }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Jabatan Kanan -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0"><strong>Jabatan (Kanan)</strong></label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_jabatan_kanan" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_jabatan_kanan" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_jabatan_kanan" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                        <select name="footer_jabatan_kanan_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                            <option value="8px" {{ ($documentParts['footer_jabatan_kanan_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                            <option value="9px" {{ ($documentParts['footer_jabatan_kanan_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                            <option value="10px" {{ ($documentParts['footer_jabatan_kanan_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                            <option value="11px" {{ ($documentParts['footer_jabatan_kanan_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                            <option value="12px" {{ ($documentParts['footer_jabatan_kanan_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="footer_jabatan_kanan_bold" value="{{ $documentParts['footer_jabatan_kanan_bold'] ?? '0' }}">
                                <input type="hidden" name="footer_jabatan_kanan_italic" value="{{ $documentParts['footer_jabatan_kanan_italic'] ?? '0' }}">
                                <input type="hidden" name="footer_jabatan_kanan_underline" value="{{ $documentParts['footer_jabatan_kanan_underline'] ?? '0' }}">
                                <input type="text" name="footer_jabatan_kanan" class="form-control form-input-preview" value="{{ $documentParts['footer_jabatan_kanan'] ?? '' }}" oninput="updateLivePreview()">
                            </div>
                            
                            <!-- Tanda Tangan Kanan -->
                            <label class="form-label mt-3"><strong>Tanda Tangan (Kanan)</strong></label>
                            <div class="upload-box mb-2">
                                <input type="file" name="footer_signature_kanan" id="footer_signature_kanan_input" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('footer_signature_kanan_input').click()">
                                    <i class="bi bi-folder2-open me-1"></i>Choose File
                                </button>
                                <span id="footer_signature_kanan_name" class="ms-2 text-muted small">No file chosen</span>
                            </div>
                            <small class="text-muted">JPG/JPEG/PNG Format</small>
                            <div class="mt-2" style="display: flex; gap: 10px;">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="footer_signature_kanan_save" style="display: none;" onclick="saveImage('footer_signature_kanan')">
                                    <i class="bi bi-check-circle me-1"></i>Save
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="footer_signature_kanan_delete" style="display: none;" onclick="deleteImage('footer_signature_kanan')">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </div>

                            <!-- Nama Kanan -->
                            <div class="mb-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0"><strong>Nama (Kanan)</strong></label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_nama_kanan" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_nama_kanan" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_nama_kanan" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                        <select name="footer_nama_kanan_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                            <option value="8px" {{ ($documentParts['footer_nama_kanan_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                            <option value="9px" {{ ($documentParts['footer_nama_kanan_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                            <option value="10px" {{ ($documentParts['footer_nama_kanan_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                            <option value="11px" {{ ($documentParts['footer_nama_kanan_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                            <option value="12px" {{ ($documentParts['footer_nama_kanan_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="footer_nama_kanan_bold" value="{{ $documentParts['footer_nama_kanan_bold'] ?? '0' }}">
                                <input type="hidden" name="footer_nama_kanan_italic" value="{{ $documentParts['footer_nama_kanan_italic'] ?? '0' }}">
                                <input type="hidden" name="footer_nama_kanan_underline" value="{{ $documentParts['footer_nama_kanan_underline'] ?? '0' }}">
                                <input type="text" name="footer_nama_kanan" class="form-control form-input-preview" value="{{ $documentParts['footer_nama_kanan'] ?? '' }}" oninput="updateLivePreview()">
                            </div>
                        </div>
                    </div>

                    <!-- Add More Fields Button -->
                    <div class="d-flex justify-content-center mt-4 mb-4">
                        <button type="button" class="btn btn-outline-success" id="addMoreFieldsBtn" onclick="toggleAdditionalFields()" style="border-color: #28a745; color: #28a745;">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Tanda Tangan Tambahan
                        </button>
                    </div>

                    <!-- Additional Signature Fields (Hidden by Default) -->
                    <div id="additionalSignatureFields" style="display: none; padding: 20px; background-color: #f8f9fa; border-radius: 8px; margin-top: 20px;">
                        <h6 class="mb-3"><strong>Tanda Tangan Tambahan (Opsional)</strong></h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Jabatan Opsional Kiri -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0"><strong>Jabatan Tambahan (Kiri)</strong></label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_jabatan_opsional_kiri" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_jabatan_opsional_kiri" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_jabatan_opsional_kiri" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                            <select name="footer_jabatan_opsional_kiri_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                                <option value="8px" {{ ($documentParts['footer_jabatan_opsional_kiri_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                                <option value="9px" {{ ($documentParts['footer_jabatan_opsional_kiri_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                                <option value="10px" {{ ($documentParts['footer_jabatan_opsional_kiri_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                                <option value="11px" {{ ($documentParts['footer_jabatan_opsional_kiri_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                                <option value="12px" {{ ($documentParts['footer_jabatan_opsional_kiri_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="footer_jabatan_opsional_kiri_bold" value="{{ $documentParts['footer_jabatan_opsional_kiri_bold'] ?? '0' }}">
                                    <input type="hidden" name="footer_jabatan_opsional_kiri_italic" value="{{ $documentParts['footer_jabatan_opsional_kiri_italic'] ?? '0' }}">
                                    <input type="hidden" name="footer_jabatan_opsional_kiri_underline" value="{{ $documentParts['footer_jabatan_opsional_kiri_underline'] ?? '0' }}">
                                    <input type="text" name="footer_jabatan_opsional_kiri" class="form-control form-input-preview" value="{{ $documentParts['footer_jabatan_opsional_kiri'] ?? '' }}" oninput="updateLivePreview()">
                                </div>

                                <!-- Tanda Tangan Opsional Kiri -->
                                <label class="form-label mt-3"><strong>Tanda Tangan Tambahan (Kiri)</strong></label>
                                <div class="upload-box mb-2">
                                    <input type="file" name="footer_signature_opsional_kiri" id="footer_signature_opsional_kiri_input" accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('footer_signature_opsional_kiri_input').click()">
                                        <i class="bi bi-folder2-open me-1"></i>Choose File
                                    </button>
                                    <span id="footer_signature_opsional_kiri_name" class="ms-2 text-muted small">No file chosen</span>
                                </div>
                                <small class="text-muted">JPG/JPEG/PNG Format</small>
                                <div class="mt-2" style="display: flex; gap: 10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="footer_signature_opsional_kiri_save" style="display: none;" onclick="saveImage('footer_signature_opsional_kiri')">
                                        <i class="bi bi-check-circle me-1"></i>Save
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="footer_signature_opsional_kiri_delete" style="display: none;" onclick="deleteImage('footer_signature_opsional_kiri')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </div>

                                <!-- Nama Opsional Kiri -->
                                <div class="mb-3 mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0"><strong>Nama Tambahan (Kiri)</strong></label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_nama_opsional_kiri" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_nama_opsional_kiri" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_nama_opsional_kiri" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                            <select name="footer_nama_opsional_kiri_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                                <option value="8px" {{ ($documentParts['footer_nama_opsional_kiri_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                                <option value="9px" {{ ($documentParts['footer_nama_opsional_kiri_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                                <option value="10px" {{ ($documentParts['footer_nama_opsional_kiri_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                                <option value="11px" {{ ($documentParts['footer_nama_opsional_kiri_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                                <option value="12px" {{ ($documentParts['footer_nama_opsional_kiri_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="footer_nama_opsional_kiri_bold" value="{{ $documentParts['footer_nama_opsional_kiri_bold'] ?? '0' }}">
                                    <input type="hidden" name="footer_nama_opsional_kiri_italic" value="{{ $documentParts['footer_nama_opsional_kiri_italic'] ?? '0' }}">
                                    <input type="hidden" name="footer_nama_opsional_kiri_underline" value="{{ $documentParts['footer_nama_opsional_kiri_underline'] ?? '0' }}">
                                    <input type="text" name="footer_nama_opsional_kiri" class="form-control form-input-preview" value="{{ $documentParts['footer_nama_opsional_kiri'] ?? '' }}" oninput="updateLivePreview()">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Jabatan Opsional Kanan -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0"><strong>Jabatan Tambahan (Kanan)</strong></label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_jabatan_opsional_kanan" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_jabatan_opsional_kanan" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_jabatan_opsional_kanan" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                            <select name="footer_jabatan_opsional_kanan_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                                <option value="8px" {{ ($documentParts['footer_jabatan_opsional_kanan_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                                <option value="9px" {{ ($documentParts['footer_jabatan_opsional_kanan_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                                <option value="10px" {{ ($documentParts['footer_jabatan_opsional_kanan_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                                <option value="11px" {{ ($documentParts['footer_jabatan_opsional_kanan_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                                <option value="12px" {{ ($documentParts['footer_jabatan_opsional_kanan_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="footer_jabatan_opsional_kanan_bold" value="{{ $documentParts['footer_jabatan_opsional_kanan_bold'] ?? '0' }}">
                                    <input type="hidden" name="footer_jabatan_opsional_kanan_italic" value="{{ $documentParts['footer_jabatan_opsional_kanan_italic'] ?? '0' }}">
                                    <input type="hidden" name="footer_jabatan_opsional_kanan_underline" value="{{ $documentParts['footer_jabatan_opsional_kanan_underline'] ?? '0' }}">
                                    <input type="text" name="footer_jabatan_opsional_kanan" class="form-control form-input-preview" value="{{ $documentParts['footer_jabatan_opsional_kanan'] ?? '' }}" oninput="updateLivePreview()">
                                </div>

                                <!-- Tanda Tangan Opsional Kanan -->
                                <label class="form-label mt-3"><strong>Tanda Tangan Tambahan (Kanan)</strong></label>
                                <div class="upload-box mb-2">
                                    <input type="file" name="footer_signature_opsional_kanan" id="footer_signature_opsional_kanan_input" accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('footer_signature_opsional_kanan_input').click()">
                                        <i class="bi bi-folder2-open me-1"></i>Choose File
                                    </button>
                                    <span id="footer_signature_opsional_kanan_name" class="ms-2 text-muted small">No file chosen</span>
                                </div>
                                <small class="text-muted">JPG/JPEG/PNG Format</small>
                                <div class="mt-2" style="display: flex; gap: 10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="footer_signature_opsional_kanan_save" style="display: none;" onclick="saveImage('footer_signature_opsional_kanan')">
                                        <i class="bi bi-check-circle me-1"></i>Save
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="footer_signature_opsional_kanan_delete" style="display: none;" onclick="deleteImage('footer_signature_opsional_kanan')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </div>

                                <!-- Nama Opsional Kanan -->
                                <div class="mb-3 mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0"><strong>Nama Tambahan (Kanan)</strong></label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn text-decoration-none" style="font-weight: bold;" data-field="footer_nama_opsional_kanan" data-style="bold" onclick="toggleTextStyle(this); updateLivePreview();" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="font-style: italic;" data-field="footer_nama_opsional_kanan" data-style="italic" onclick="toggleTextStyle(this); updateLivePreview();" title="Italic">I</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary style-btn" style="text-decoration: underline;" data-field="footer_nama_opsional_kanan" data-style="underline" onclick="toggleTextStyle(this); updateLivePreview();" title="Underline">U</button>
                                            <select name="footer_nama_opsional_kanan_font_size" class="form-select form-select-sm form-input-preview font-size-select" onchange="updateLivePreview()">
                                                <option value="8px" {{ ($documentParts['footer_nama_opsional_kanan_font_size'] ?? '9px') == '8px' ? 'selected' : '' }}>8px</option>
                                                <option value="9px" {{ ($documentParts['footer_nama_opsional_kanan_font_size'] ?? '9px') == '9px' ? 'selected' : '' }}>9px</option>
                                                <option value="10px" {{ ($documentParts['footer_nama_opsional_kanan_font_size'] ?? '9px') == '10px' ? 'selected' : '' }}>10px</option>
                                                <option value="11px" {{ ($documentParts['footer_nama_opsional_kanan_font_size'] ?? '9px') == '11px' ? 'selected' : '' }}>11px</option>
                                                <option value="12px" {{ ($documentParts['footer_nama_opsional_kanan_font_size'] ?? '9px') == '12px' ? 'selected' : '' }}>12px</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="footer_nama_opsional_kanan_bold" value="{{ $documentParts['footer_nama_opsional_kanan_bold'] ?? '0' }}">
                                    <input type="hidden" name="footer_nama_opsional_kanan_italic" value="{{ $documentParts['footer_nama_opsional_kanan_italic'] ?? '0' }}">
                                    <input type="hidden" name="footer_nama_opsional_kanan_underline" value="{{ $documentParts['footer_nama_opsional_kanan_underline'] ?? '0' }}">
                                    <input type="text" name="footer_nama_opsional_kanan" class="form-control form-input-preview" value="{{ $documentParts['footer_nama_opsional_kanan'] ?? '' }}" oninput="updateLivePreview()">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-outline-danger" onclick="toggleAdditionalFields()">
                                <i class="bi bi-minus-circle me-1"></i>Sembunyikan Tanda Tangan Tambahan
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Step 5: Review -->
                <div class="step-section" id="step-5" style="{{ $currentStep == 5 ? '' : 'display: none;' }}">
                    <h5 class="fw-bold mb-4">Review Dokumen</h5>
                    <p class="text-muted mb-4">Periksa kembali seluruh dokumen Anda. Klik tombol "Kembali" jika perlu melakukan perubahan.</p>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Tips:</strong> Preview di sebelah kanan menampilkan hasil final dokumen Anda setelah diunduh.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-primary">
                                <i class="bi bi-file-text me-2"></i>Data Terisi
                            </h6>
                            <div class="card bg-light p-3">
                                <p><strong>Kop Surat:</strong> <span id="review-kop-status" class="badge bg-success">Terisi</span></p>
                                <p><strong>Header:</strong> <span id="review-header-status" class="badge bg-secondary">Kosong</span></p>
                                <p><strong>Body:</strong> <span id="review-body-status" class="badge bg-secondary">Kosong</span></p>
                                <p><strong>Footer:</strong> <span id="review-footer-status" class="badge bg-secondary">Kosong</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-success">
                                <i class="bi bi-check-circle me-2"></i>Status Pengisian
                            </h6>
                            <div class="card bg-light p-3">
                                <div id="review-completion" style="font-size: 24px; font-weight: bold; text-align: center; margin: 10px 0;">
                                    0%
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div id="review-progress" class="progress-bar bg-success" style="width: 0%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold mb-3">Catatan</h6>
                        <textarea class="form-control" rows="4" name="document_notes" placeholder="Tambahkan catatan atau keterangan untuk dokumen ini (opsional)..."></textarea>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex gap-2 mt-5">
                    <button type="button" class="btn btn-outline-secondary" id="prevStepBtn" onclick="previousStep()" style="display: none;">
                        <i class="bi bi-arrow-left me-2"></i><span id="prevStepText">Sebelumnya</span>
                    </button>
                    <button type="button" class="btn btn-primary ms-auto" id="nextStepBtn" onclick="nextStep()">
                        <i class="bi bi-save me-2"></i>Simpan & Lanjut
                    </button>
                    <button type="button" class="btn btn-success ms-auto" id="downloadBtn" style="display: none;" data-bs-toggle="modal" data-bs-target="#downloadFormatModal">
                        <i class="bi bi-download me-2"></i>Download & Simpan
                    </button>
                </div>
            </form>

            <!-- Download Format Modal -->
            <div class="modal fade" id="downloadFormatModal" tabindex="-1" aria-labelledby="downloadFormatLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="downloadFormatLabel">Pilih Format Download</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-4">Pilih format file yang ingin Anda download:</p>
                            <div class="d-grid gap-3">
                                <button type="button" class="btn btn-outline-primary btn-lg" onclick="downloadAsPDF()">
                                    <i class="bi bi-file-pdf me-2"></i>Download sebagai PDF
                                </button>
                                <button type="button" class="btn btn-outline-info btn-lg" onclick="downloadAsWord()">
                                    <i class="bi bi-file-word me-2"></i>Download sebagai Word
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- Right Side: Preview -->
            <div style="flex: 0 0 55%; min-width: 0;">
                <div class="sticky-top" style="top: 100px; max-height: calc(100vh - 120px); overflow-y: auto;">
                    <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 fw-bold">Preview (A4)</h6>
                    </div>
                    <div style="position: relative; width: 100%; padding-bottom: 141.4%; background: #f0f0f0; border-radius: 4px; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow-y: auto; padding: 12px; box-sizing: border-box;">
                            <div id="previewContent" style="width: 100%; background: white; padding: 40px; box-sizing: border-box; font-size: 10px; line-height: 1.4; min-height: 100%; font-family: 'Times New Roman', serif;">
                                <!-- Live Preview Content akan muncul di sini -->
                                <p style="color: #999; text-align: center; margin-top: 40px;">Preview akan muncul saat Anda mengisi form...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .upload-box {
        border: 2px dashed #dee2e6;
        padding: 15px;
        border-radius: 0.375rem;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .upload-box:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
    }

    /* Truncate file names in upload boxes */
    .upload-box span[id*="_name"] {
        display: inline-block;
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }

    /* Compact style buttons for text formatting */
    .style-btn {
        width: 20px !important;
        height: 20px !important;
        padding: 1px !important;
        font-size: 10px !important;
        line-height: 1 !important;
        min-width: auto !important;
    }

    .style-btn.btn-sm {
        padding: 1px !important;
    }

    /* Compact font size selector */
    .font-size-select {
        max-width: 60px !important;
        font-size: 10px !important;
        padding: 2px 4px !important;
        height: auto !important;
        line-height: 1.2 !important;
    }

    .review-section {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 0.375rem;
    }

    .step-section {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .btn-step {
        transition: all 0.2s ease;
    }

    .btn-step:hover {
        transform: scale(1.1);
    }

    .sticky-top {
        top: 100px !important;
    }

    /* A4 Preview Styling */
    .a4-preview {
        position: relative;
        width: 100%;
        padding-bottom: 141.4%;
        background: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .a4-preview > div {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        padding: 12px;
        box-sizing: border-box;
    }

    .a4-content {
        width: 100%;
        background: white;
        padding: 15px;
        box-sizing: border-box;
        font-size: 10px;
        line-height: 1.4;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .d-flex[style*="flex-wrap: nowrap"] {
            flex-wrap: wrap !important;
        }

        .d-flex[style*="flex-wrap: nowrap"] > div:first-child {
            flex: 0 0 100% !important;
            margin-bottom: 2rem;
        }

        .d-flex[style*="flex-wrap: nowrap"] > div:last-child {
            flex: 0 0 100% !important;
        }

        .sticky-top {
            position: static !important;
            margin-top: 2rem;
        }
    }

    @media (max-width: 768px) {
        .sticky-top {
            position: static !important;
            margin-top: 1rem;
        }
    }
</style>

<script>
    function goToStep(step) {
        // Hide all sections
        for(let i = 1; i <= 5; i++) {
            document.getElementById('step-' + i).style.display = 'none';
        }
        // Show selected step
        document.getElementById('step-' + step).style.display = 'block';
        
        // Update step indicator
        updateStepIndicator(step);
        
        // Update button visibility
        updateButtonState(step);
    }

    function updateButtonState(step) {
        const nextStepBtn = document.getElementById('nextStepBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const prevStepBtn = document.getElementById('prevStepBtn');
        
        if(step < 5) {
            nextStepBtn.style.display = 'block';
            downloadBtn.style.display = 'none';
        } else {
            nextStepBtn.style.display = 'none';
            downloadBtn.style.display = 'block';
        }
        
        // Update previous step button visibility
        if(step === 1) {
            prevStepBtn.style.display = 'none';
        } else {
            prevStepBtn.style.display = 'block';
        }
    }

    function nextStep() {
        const current = getCurrentStep();
        if(current < 5) {
            // Set current step dan submit form
            console.log('Submitting form for next step:', current + 1);
            document.getElementById('current_step_input').value = current + 1;
            document.getElementById('documentForm').submit();
        }
    }

    function previousStep() {
        const current = getCurrentStep();
        if(current > 1) {
            // Set current step dan submit form
            console.log('Submitting form for previous step:', current - 1);
            document.getElementById('current_step_input').value = current - 1;
            document.getElementById('documentForm').submit();
        }
    }

    function getCurrentStep() {
        for(let i = 1; i <= 5; i++) {
            if(document.getElementById('step-' + i).style.display !== 'none') {
                return i;
            }
        }
        return 1;
    }

    function toggleAdditionalFields() {
        const additionalFields = document.getElementById('additionalSignatureFields');
        const addBtn = document.getElementById('addMoreFieldsBtn');
        
        if(additionalFields.style.display === 'none') {
            additionalFields.style.display = 'block';
            addBtn.style.display = 'none';
        } else {
            additionalFields.style.display = 'none';
            addBtn.style.display = 'block';
        }
    }

    function updateStepIndicator(step) {
        const indicators = document.querySelectorAll('[onclick^="goToStep"]');
        indicators.forEach((ind, index) => {
            if(index + 1 <= step) {
                ind.style.backgroundColor = '#0d6efd';
            } else {
                ind.style.backgroundColor = '#e9ecef';
            }
        });
    }

    // Live Preview Functions
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize layout settings from server
        window.layoutSettings = @json($layoutSettings ?? []);
        
        // Restore style buttons from hidden inputs
        restoreStyleButtons();
        
        // Form input live preview
        const formInputs = document.querySelectorAll('.form-input-preview');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                updateLivePreview();
                updateReviewStatus();
            });
        });

        // File input handlers
        document.getElementById('kop_left_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('kop_left_name').textContent = name;
            document.getElementById('kop_left_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        document.getElementById('kop_right_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('kop_right_name').textContent = name;
            document.getElementById('kop_right_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        // Footer signature file inputs
        document.getElementById('footer_signature_kiri_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('footer_signature_kiri_name').textContent = name;
            document.getElementById('footer_signature_kiri_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        document.getElementById('footer_signature_kanan_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('footer_signature_kanan_name').textContent = name;
            document.getElementById('footer_signature_kanan_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        // Optional signature file inputs
        const optionalSignatureInputs = ['footer_signature_opsional_kiri', 'footer_signature_opsional_kanan'];
        optionalSignatureInputs.forEach(fieldName => {
            const inputElement = document.getElementById(fieldName + '_input');
            if(inputElement) {
                inputElement.addEventListener('change', function() {
                    const name = this.files[0]?.name || 'No file chosen';
                    const nameElement = document.getElementById(fieldName + '_name');
                    const saveButton = document.getElementById(fieldName + '_save');
                    if(nameElement) nameElement.textContent = name;
                    if(saveButton) saveButton.style.display = this.files[0] ? 'inline-block' : 'none';
                });
            }
        });

        updateLivePreview();
        updateReviewStatus();

        // File input handlers untuk image upload
        document.getElementById('kop_left_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('kop_left_name').textContent = name;
            document.getElementById('kop_left_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        document.getElementById('kop_right_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('kop_right_name').textContent = name;
            document.getElementById('kop_right_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        // Footer signature file inputs
        document.getElementById('footer_signature_kiri_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('footer_signature_kiri_name').textContent = name;
            document.getElementById('footer_signature_kiri_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        document.getElementById('footer_signature_kanan_input').addEventListener('change', function() {
            const name = this.files[0]?.name || 'No file chosen';
            document.getElementById('footer_signature_kanan_name').textContent = name;
            document.getElementById('footer_signature_kanan_save').style.display = this.files[0] ? 'inline-block' : 'none';
        });

        // Restore file names and delete buttons on page load
        restoreFileNames();

        // Update button state on page load
        const currentStep = getCurrentStep();
        updateButtonState(currentStep);
    });

    function restoreStyleButtons() {
        // Get all style buttons and check their hidden input values
        const styleButtons = document.querySelectorAll('[data-field][data-style]');
        styleButtons.forEach(button => {
            const field = button.getAttribute('data-field');
            const style = button.getAttribute('data-style');
            const hiddenInput = document.querySelector(`input[name="${field}_${style}"]`);
            
            if (hiddenInput && hiddenInput.value === '1') {
                button.classList.add('active');
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-secondary');
            }
        });
    }

    function toggleTextStyle(button) {
        button.classList.toggle('active');
        const field = button.getAttribute('data-field');
        const style = button.getAttribute('data-style');
        const hiddenInput = document.querySelector(`input[name="${field}_${style}"]`);
        
        if (hiddenInput) {
            hiddenInput.value = button.classList.contains('active') ? '1' : '0';
        }
        
        // Visual feedback
        if (button.classList.contains('active')) {
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-secondary');
        } else {
            button.classList.add('btn-outline-secondary');
            button.classList.remove('btn-secondary');
        }
    }

    function getTextStyleString(field) {
        const bold = document.querySelector(`input[name="${field}_bold"]`)?.value === '1';
        const italic = document.querySelector(`input[name="${field}_italic"]`)?.value === '1';
        const underline = document.querySelector(`input[name="${field}_underline"]`)?.value === '1';
        
        let style = '';
        if (bold) style += 'font-weight: bold; ';
        if (italic) style += 'font-style: italic; ';
        if (underline) style += 'text-decoration: underline; ';
        
        return style;
    }

    function updateReviewStatus() {
        // Check each section
        const kopInstansi = document.querySelector('textarea[name="kop_instansi"], input[name="kop_instansi"]')?.value || '';
        const kopNama = document.querySelector('textarea[name="kop_nama"], input[name="kop_nama"]')?.value || '';
        const headerTempatTanggal = document.querySelector('input[name="header_tempat_tanggal"]')?.value || '';
        const headerNomor = document.querySelector('input[name="header_nomor"]')?.value || '';
        const bodyParagraph1 = document.querySelector('textarea[name="body_paragraph1"]')?.value || '';
        
        // Check if ANY footer field has content
        const footerJabatanKiri = document.querySelector('input[name="footer_jabatan_kiri"]')?.value || '';
        const footerNamaKiri = document.querySelector('input[name="footer_nama_kiri"]')?.value || '';
        const footerJabatanKanan = document.querySelector('input[name="footer_jabatan_kanan"]')?.value || '';
        const footerNamaKanan = document.querySelector('input[name="footer_nama_kanan"]')?.value || '';
        const hasFooterData = footerJabatanKiri || footerNamaKiri || footerJabatanKanan || footerNamaKanan;

        // Update badges
        document.getElementById('review-kop-status').textContent = (kopInstansi || kopNama) ? 'Terisi' : 'Kosong';
        document.getElementById('review-kop-status').className = (kopInstansi || kopNama) ? 'badge bg-success' : 'badge bg-secondary';

        document.getElementById('review-header-status').textContent = (headerTempatTanggal || headerNomor) ? 'Terisi' : 'Kosong';
        document.getElementById('review-header-status').className = (headerTempatTanggal || headerNomor) ? 'badge bg-success' : 'badge bg-secondary';

        document.getElementById('review-body-status').textContent = bodyParagraph1 ? 'Terisi' : 'Kosong';
        document.getElementById('review-body-status').className = bodyParagraph1 ? 'badge bg-success' : 'badge bg-secondary';

        document.getElementById('review-footer-status').textContent = hasFooterData ? 'Terisi' : 'Kosong';
        document.getElementById('review-footer-status').className = hasFooterData ? 'badge bg-success' : 'badge bg-secondary';

        // Calculate completion percentage
        const total = 4;
        let filled = 0;
        if(kopInstansi || kopNama) filled++;
        if(headerTempatTanggal || headerNomor) filled++;
        if(bodyParagraph1) filled++;
        if(hasFooterData) filled++;

        const percentage = Math.round((filled / total) * 100);
        document.getElementById('review-completion').textContent = percentage + '%';
        document.getElementById('review-progress').style.width = percentage + '%';
    }

    function restoreFileNames() {
        // List of all image fields
        const imageFields = ['kop_left', 'kop_right', 'footer_signature_kiri', 'footer_signature_kanan', 'footer_signature_opsional_kiri', 'footer_signature_opsional_kanan'];
        
        imageFields.forEach(fieldName => {
            const imageName = sessionStorage.getItem('filename_' + fieldName);
            const imageData = sessionStorage.getItem('preview_' + fieldName);
            
            if(imageName && imageData) {
                // Check if element exists (optional fields might not be visible)
                const nameElement = document.getElementById(fieldName + '_name');
                const deleteButton = document.getElementById(fieldName + '_delete');
                const saveButton = document.getElementById(fieldName + '_save');
                
                if(nameElement) {
                    // Restore file name display
                    nameElement.textContent = imageName;
                }
                if(deleteButton) {
                    // Show delete button
                    deleteButton.style.display = 'inline-block';
                }
                if(saveButton) {
                    // Hide save button
                    saveButton.style.display = 'none';
                }
            }
        });
    }

    function saveImage(fieldName) {
        const fileInput = document.getElementById(fieldName + '_input');
        const file = fileInput.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageData = 'preview_' + fieldName;
                const imageName = 'filename_' + fieldName;
                sessionStorage.setItem(imageData, e.target.result);
                sessionStorage.setItem(imageName, file.name);
                document.getElementById(fieldName + '_delete').style.display = 'inline-block';
                updateLivePreview();
            };
            reader.readAsDataURL(file);
        }
    }

    function deleteImage(fieldName) {
        const imageData = 'preview_' + fieldName;
        const imageName = 'filename_' + fieldName;
        sessionStorage.removeItem(imageData);
        sessionStorage.removeItem(imageName);
        document.getElementById(fieldName + '_input').value = '';
        document.getElementById(fieldName + '_name').textContent = 'No file chosen';
        document.getElementById(fieldName + '_save').style.display = 'none';
        document.getElementById(fieldName + '_delete').style.display = 'none';
        updateLivePreview();
    }

    function updateLivePreview() {
        const previewContent = document.getElementById('previewContent');
        
        // Get layout settings from data attribute or use defaults
        const layoutSettings = window.layoutSettings || {};
        const kopSpacing = layoutSettings.kop_spacing || '20';
        const kopImageWidth = layoutSettings.kop_image_width || '60';
        const kopImageHeight = layoutSettings.kop_image_height || '80';
        const headerAlignment = layoutSettings.header_alignment || 'center';
        const signatureWidth = layoutSettings.signature_width || '80';
        
        // Debug
        console.log('Layout Settings:', layoutSettings);
        console.log('Kop Spacing:', kopSpacing);
        console.log('Header Alignment:', headerAlignment);
        console.log('Signature Width:', signatureWidth);
        
        // ===== KOP SURAT =====
        const kopInstansi = document.querySelector('textarea[name="kop_instansi"], input[name="kop_instansi"]')?.value || '';
        const kopInstansiSize = document.querySelector('select[name="kop_instansi_font_size"]')?.value || '12px';
        
        const kopNama = document.querySelector('textarea[name="kop_nama"], input[name="kop_nama"]')?.value || '';
        const kopNamaSize = document.querySelector('select[name="kop_nama_font_size"]')?.value || '10px';
        
        const kopAlamat = document.querySelector('textarea[name="kop_alamat"], input[name="kop_alamat"]')?.value || '';
        const kopAlamatSize = document.querySelector('select[name="kop_alamat_font_size"]')?.value || '9px';
        
        const kopTelp = document.querySelector('input[name="kop_telp"]')?.value || '';
        const kopTelpSize = document.querySelector('select[name="kop_telp_font_size"]')?.value || '9px';
        
        const kopEmail = document.querySelector('input[name="kop_email"]')?.value || '';
        const kopEmailSize = document.querySelector('select[name="kop_email_font_size"]')?.value || '9px';
        
        const leftImageData = sessionStorage.getItem('preview_kop_left') || '';
        const rightImageData = sessionStorage.getItem('preview_kop_right') || '';

        // ===== HEADER =====
        const headerTempatTanggal = document.querySelector('input[name="header_tempat_tanggal"]')?.value || '';
        const headerTempatTanggalSize = document.querySelector('select[name="header_tempat_tanggal_font_size"]')?.value || '12px';
        const headerJudul = document.querySelector('input[name="header_judul"]')?.value || '';
        const headerJudulSize = document.querySelector('select[name="header_judul_font_size"]')?.value || '14px';
        const headerNomor = document.querySelector('input[name="header_nomor"]')?.value || '';
        const headerNomorSize = document.querySelector('select[name="header_nomor_font_size"]')?.value || '11px';
        const headerLampiran = document.querySelector('input[name="header_lampiran"]')?.value || '';
        const headerLampiranSize = document.querySelector('select[name="header_lampiran_font_size"]')?.value || '11px';
        const headerPerihal = document.querySelector('input[name="header_perihal"]')?.value || '';
        const headerPerihalSize = document.querySelector('select[name="header_perihal_font_size"]')?.value || '11px';
        const headerBody = document.querySelector('textarea[name="header_body"]')?.value || '';
        const headerBodySize = document.querySelector('select[name="header_body_font_size"]')?.value || '11px';

        // ===== BODY =====
        const bodyParagraph1 = document.querySelector('textarea[name="body_paragraph1"]')?.value || '';
        const bodyParagraph1Size = document.querySelector('select[name="body_paragraph1_font_size"]')?.value || '10px';
        const bodyHari = document.querySelector('input[name="body_hari"]')?.value || '';
        const bodyHariSize = document.querySelector('select[name="body_hari_font_size"]')?.value || '10px';
        const bodyTanggal = document.querySelector('input[name="body_tanggal"]')?.value || '';
        const bodyTanggalSize = document.querySelector('select[name="body_tanggal_font_size"]')?.value || '10px';
        const bodyWaktu = document.querySelector('input[name="body_waktu"]')?.value || '';
        const bodyWaktuSize = document.querySelector('select[name="body_waktu_font_size"]')?.value || '10px';
        const bodyTempat = document.querySelector('input[name="body_tempat"]')?.value || '';
        const bodyTempatSize = document.querySelector('select[name="body_tempat_font_size"]')?.value || '10px';
        const bodyParagraph2 = document.querySelector('textarea[name="body_paragraph2"]')?.value || '';
        const bodyParagraph2Size = document.querySelector('select[name="body_paragraph2_font_size"]')?.value || '10px';

        // ===== FOOTER =====
        const footerJabatanKiri = document.querySelector('input[name="footer_jabatan_kiri"]')?.value || '';
        const footerJabatanKiriSize = document.querySelector('select[name="footer_jabatan_kiri_font_size"]')?.value || '9px';
        const footerNamaKiri = document.querySelector('input[name="footer_nama_kiri"]')?.value || '';
        const footerNamaKiriSize = document.querySelector('select[name="footer_nama_kiri_font_size"]')?.value || '9px';
        const footerJabatanKanan = document.querySelector('input[name="footer_jabatan_kanan"]')?.value || '';
        const footerJabatanKananSize = document.querySelector('select[name="footer_jabatan_kanan_font_size"]')?.value || '9px';
        const footerNamaKanan = document.querySelector('input[name="footer_nama_kanan"]')?.value || '';
        const footerNamaKananSize = document.querySelector('select[name="footer_nama_kanan_font_size"]')?.value || '9px';
        
        // Optional Footer Fields
        const footerJabatanOpsionalKiri = document.querySelector('input[name="footer_jabatan_opsional_kiri"]')?.value || '';
        const footerJabatanOpsionalKiriSize = document.querySelector('select[name="footer_jabatan_opsional_kiri_font_size"]')?.value || '9px';
        const footerNamaOpsionalKiri = document.querySelector('input[name="footer_nama_opsional_kiri"]')?.value || '';
        const footerNamaOpsionalKiriSize = document.querySelector('select[name="footer_nama_opsional_kiri_font_size"]')?.value || '9px';
        const footerJabatanOpsionalKanan = document.querySelector('input[name="footer_jabatan_opsional_kanan"]')?.value || '';
        const footerJabatanOpsionalKananSize = document.querySelector('select[name="footer_jabatan_opsional_kanan_font_size"]')?.value || '9px';
        const footerNamaOpsionalKanan = document.querySelector('input[name="footer_nama_opsional_kanan"]')?.value || '';
        const footerNamaOpsionalKananSize = document.querySelector('select[name="footer_nama_opsional_kanan_font_size"]')?.value || '9px';
        
        const sigKiriData = sessionStorage.getItem('preview_footer_signature_kiri') || '';
        const sigKananData = sessionStorage.getItem('preview_footer_signature_kanan') || '';
        const sigOpsionalKiriData = sessionStorage.getItem('preview_footer_signature_opsional_kiri') || '';
        const sigOpsionalKananData = sessionStorage.getItem('preview_footer_signature_opsional_kanan') || '';

        // Build preview HTML
        let previewHTML = '';
        
        // KOP SURAT
        if(kopInstansi || kopNama || kopAlamat || kopTelp || kopEmail || leftImageData || rightImageData) {
            // Determine if only one image exists
            const hasOnlyOneImage = (leftImageData || rightImageData) && !(leftImageData && rightImageData);
            const textFlex = hasOnlyOneImage ? 'none' : '1';
            
            previewHTML += `
                <div style="padding: 0px 20px; margin: 0 -30px;">
                    <div style="display: flex; justify-content: center; align-items: flex-start; gap: 30px; margin-top: 40px; margin-bottom: 10px;">
                        ${leftImageData ? `<div style="flex-shrink: 0;"><img src="${leftImageData}" style="max-width: ${kopImageWidth}px; height: auto;"></div>` : ''}
                        <div style="text-align: center; flex: ${textFlex};">
                            ${kopInstansi ? `<h6 style="margin: 0; font-size: ${kopInstansiSize}; ${getTextStyleString('kop_instansi')}; line-height: 1.2;">${kopInstansi}</h6>` : ''}
                            ${kopNama ? `<p style="margin: 5px 0; font-size: ${kopNamaSize}; ${getTextStyleString('kop_nama')}; line-height: 1.2;">${kopNama}</p>` : ''}
                            ${kopAlamat ? `<p style="margin: 0; font-size: ${kopAlamatSize}; color: #000; ${getTextStyleString('kop_alamat')}; line-height: 1.2;">${kopAlamat}</p>` : ''}
                            ${kopTelp || kopEmail ? `<p style="margin: 0; font-size: ${kopTelpSize}; color: #000;">` : ''}
                            ${kopTelp ? `<span style="${getTextStyleString('kop_telp')}">Telp. ${kopTelp}</span>` : ''}
                            ${kopTelp && kopEmail ? ' | ' : ''}
                            ${kopEmail ? `<span style="${getTextStyleString('kop_email')}">Email: ${kopEmail}</span>` : ''}
                            ${kopTelp || kopEmail ? `</p>` : ''}
                        </div>
                        ${rightImageData ? `<div style="flex-shrink: 0;"><img src="${rightImageData}" style="max-width: ${kopImageWidth}px; height: auto;"></div>` : ''}
                    </div>
                    <hr style="margin: 10px 0; border: none; border-top: 3px solid #000;">
                </div>
            `;
        }

        // HEADER
        if(headerTempatTanggal || headerNomor || headerLampiran || headerPerihal || headerBody) {
            previewHTML += `
                <div style="padding: 0 70px; margin: 0 -30px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; gap: 30px; margin-bottom: 10px;">
                        <div style="flex: 0 0 auto;">
                            ${headerNomor ? `<p style="margin: 0 0 3px 0; font-size: ${headerNomorSize}; ${getTextStyleString('header_nomor')}">No&nbsp;: ${headerNomor}</p>` : ''}
                            ${headerLampiran ? `<p style="margin: 0 0 3px 0; font-size: ${headerLampiranSize}; ${getTextStyleString('header_lampiran')}">Lamp&nbsp;: ${headerLampiran}</p>` : ''}
                            ${headerPerihal ? `<p style="margin: 0; font-size: ${headerPerihalSize}; ${getTextStyleString('header_perihal')}">Hal&nbsp;: ${headerPerihal}</p>` : ''}
                        </div>
                        <div style="flex: 1; text-align: right;">
                            ${headerTempatTanggal ? `<p style="margin: 0; font-size: ${headerTempatTanggalSize}; ${getTextStyleString('header_tempat_tanggal')}">${headerTempatTanggal}</p>` : ''}
                        </div>
                    </div>
                
                    ${headerBody ? `<p style="margin: 10px 0; font-size: ${headerBodySize}; ${getTextStyleString('header_body')}; white-space: pre-wrap;">${headerBody}</p>` : ''}
                </div>
                
            `;
        }

        // BODY
        if(bodyParagraph1 || bodyHari || bodyTanggal || bodyWaktu || bodyTempat || bodyParagraph2) {
            previewHTML += `
                <div style="padding: 0 70px; margin: 0 -30px; margin-bottom: 15px;">
                    ${bodyParagraph1 ? `<p style="margin: 10px 0; font-size: ${bodyParagraph1Size}; ${getTextStyleString('body_paragraph1')}; white-space: pre-wrap; word-wrap: break-word;">${bodyParagraph1}</p>` : ''}
                    ${(bodyHari || bodyTanggal || bodyWaktu || bodyTempat) ? `
                        <div style="margin: 15px 0;">
                            ${bodyHari ? `<p style="margin: 3px 0; font-size: ${bodyHariSize}; ${getTextStyleString('body_hari')}">Hari&nbsp;: ${bodyHari}</p>` : ''}
                            ${bodyTanggal ? `<p style="margin: 3px 0; font-size: ${bodyTanggalSize}; ${getTextStyleString('body_tanggal')}">Tanggal&nbsp;: ${bodyTanggal}</p>` : ''}
                            ${bodyWaktu ? `<p style="margin: 3px 0; font-size: ${bodyWaktuSize}; ${getTextStyleString('body_waktu')}">Waktu&nbsp;: ${bodyWaktu}</p>` : ''}
                            ${bodyTempat ? `<p style="margin: 3px 0; font-size: ${bodyTempatSize}; ${getTextStyleString('body_tempat')}">Tempat&nbsp;: ${bodyTempat}</p>` : ''}
                        </div>
                    ` : ''}
                    ${bodyParagraph2 ? `<p style="margin: 10px 0; font-size: ${bodyParagraph2Size}; ${getTextStyleString('body_paragraph2')}; white-space: pre-wrap; word-wrap: break-word;">${bodyParagraph2}</p>` : ''}
                </div>
            `;
        }

        // FOOTER
        if(footerJabatanKiri || footerNamaKiri || footerJabatanKanan || footerNamaKanan || footerJabatanOpsionalKiri || footerNamaOpsionalKiri || footerJabatanOpsionalKanan || footerNamaOpsionalKanan) {
            previewHTML += `
                <div style="padding: 0 40px; margin: 0 -30px; margin-top: 30px; text-align: center; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        ${footerJabatanKiri ? `<p style="margin: 0; font-size: ${footerJabatanKiriSize}; ${getTextStyleString('footer_jabatan_kiri')}">${footerJabatanKiri}</p>` : ''}
                        ${sigKiriData ? `<div style="margin: 20px 0; display: flex; justify-content: center;"><img src="${sigKiriData}" style="max-width: 100%; max-height: ${signatureWidth}px; width: auto;"></div>` : (footerJabatanKiri || footerNamaKiri ? `<p style="margin: 20px 0 5px 0; font-size: 9px; border-bottom: 1px solid #333; width: 80px;">&nbsp;</p>` : '')}
                        ${footerNamaKiri ? `<p style="margin: 5px 0; font-size: ${footerNamaKiriSize}; ${getTextStyleString('footer_nama_kiri')}">${footerNamaKiri}</p>` : ''}
                        
                        ${footerJabatanOpsionalKiri ? `<p style="margin: 15px 0 0 0; font-size: ${footerJabatanOpsionalKiriSize}; ${getTextStyleString('footer_jabatan_opsional_kiri')}">${footerJabatanOpsionalKiri}</p>` : ''}
                        ${sigOpsionalKiriData ? `<div style="margin: 20px 0; display: flex; justify-content: center;"><img src="${sigOpsionalKiriData}" style="max-width: 100%; max-height: ${signatureWidth}px; width: auto;"></div>` : (footerJabatanOpsionalKiri || footerNamaOpsionalKiri ? `<p style="margin: 20px 0 5px 0; font-size: 9px; border-bottom: 1px solid #333; width: 80px;">&nbsp;</p>` : '')}
                        ${footerNamaOpsionalKiri ? `<p style="margin: 5px 0; font-size: ${footerNamaOpsionalKiriSize}; ${getTextStyleString('footer_nama_opsional_kiri')}">${footerNamaOpsionalKiri}</p>` : ''}
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        ${footerJabatanKanan ? `<p style="margin: 0; font-size: ${footerJabatanKananSize}; ${getTextStyleString('footer_jabatan_kanan')}">${footerJabatanKanan}</p>` : ''}
                        ${sigKananData ? `<div style="margin: 20px 0; display: flex; justify-content: center;"><img src="${sigKananData}" style="max-width: 100%; max-height: ${signatureWidth}px; width: auto;"></div>` : (footerJabatanKanan || footerNamaKanan ? `<p style="margin: 20px 0 5px 0; font-size: 9px; border-bottom: 1px solid #333; width: 80px;">&nbsp;</p>` : '')}
                        ${footerNamaKanan ? `<p style="margin: 5px 0; font-size: ${footerNamaKananSize}; ${getTextStyleString('footer_nama_kanan')}">${footerNamaKanan}</p>` : ''}
                        
                        ${footerJabatanOpsionalKanan ? `<p style="margin: 15px 0 0 0; font-size: ${footerJabatanOpsionalKananSize}; ${getTextStyleString('footer_jabatan_opsional_kanan')}">${footerJabatanOpsionalKanan}</p>` : ''}
                        ${sigOpsionalKananData ? `<div style="margin: 20px 0; display: flex; justify-content: center;"><img src="${sigOpsionalKananData}" style="max-width: 100%; max-height: ${signatureWidth}px; width: auto;"></div>` : (footerJabatanOpsionalKanan || footerNamaOpsionalKanan ? `<p style="margin: 20px 0 5px 0; font-size: 9px; border-bottom: 1px solid #333; width: 80px;">&nbsp;</p>` : '')}
                        ${footerNamaOpsionalKanan ? `<p style="margin: 5px 0; font-size: ${footerNamaOpsionalKananSize}; ${getTextStyleString('footer_nama_opsional_kanan')}">${footerNamaOpsionalKanan}</p>` : ''}
                    </div>
                </div>

            `;
        }

        // Default message
        if(!previewHTML) {
            previewHTML = '<p style="color: #999; text-align: center; margin-top: 40px;">Preview akan muncul saat Anda mengisi form...</p>';
        }

        previewContent.innerHTML = previewHTML;
    }

    // Download functions
    function downloadAsPDF() {
        const docTitle = document.getElementById('documentTitle').value || 'dokumen';
        const filename = sanitizeFilename(docTitle) + '.pdf';
        const previewElement = document.getElementById('previewContent');
        
        // Clone element to preserve original
        const clonedElement = previewElement.cloneNode(true);
        clonedElement.style.padding = '20mm';
        clonedElement.style.margin = '0';
        clonedElement.style.width = '100%';
        
        // Create temporary div for pdf conversion
        const container = document.createElement('div');
        container.style.display = 'none';
        container.appendChild(clonedElement);
        document.body.appendChild(container);
        
        // Use jsPDF with html method for text selectability
        const opt = {
            margin: 10,
            filename: filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            },
            jsPDF: { 
                orientation: 'portrait', 
                unit: 'mm', 
                format: 'a4'
            }
        };
        
        html2pdf().set(opt).from(clonedElement).save().finally(() => {
            document.body.removeChild(container);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('downloadFormatModal'));
            if(modal) modal.hide();
        });
    }

    function downloadAsWord() {
        const docTitle = document.getElementById('documentTitle').value || 'dokumen';
        const filename = sanitizeFilename(docTitle) + '.doc';
        const previewElement = document.getElementById('previewContent');
        
        // Get computed style dari preview element
        const computedStyle = window.getComputedStyle(previewElement);
        const fontSize = computedStyle.fontSize || '10px';
        const lineHeight = computedStyle.lineHeight || '1.4';
        const fontFamily = computedStyle.fontFamily || "'Times New Roman', serif";
        
        // Clone and get inner HTML
        const clonedElement = previewElement.cloneNode(true);
        const previewHTML = clonedElement.innerHTML;
        
        // Create HTML dengan proper styling yang match preview
        const htmlContent = `
            <!DOCTYPE html>
            <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head>
                <meta charset='utf-8'>
                <title>${docTitle}</title>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body {
                        font-family: 'Times New Roman', serif;
                        font-size: 11pt;
                        line-height: 1.4;
                        color: #000;
                        padding: 20mm;
                        margin: 0;
                        width: 100%;
                    }
                    div, p, span, h1, h2, h3, h4, h5, h6 {
                        font-family: 'Times New Roman', serif;
                        font-size: 11pt;
                        line-height: 1.4;
                        color: #000;
                    }
                    p {
                        margin: 0;
                        padding: 0;
                        font-size: 11pt;
                        line-height: 1.4;
                    }
                    h1 { 
                        font-size: 16pt;
                        font-weight: bold;
                        margin: 10px 0 5px 0;
                    }
                    h2 { 
                        font-size: 14pt;
                        font-weight: bold;
                        margin: 10px 0 5px 0;
                    }
                    h3 { 
                        font-size: 12pt;
                        font-weight: bold;
                        margin: 10px 0 5px 0;
                    }
                    h4, h5, h6 { 
                        font-size: 11pt;
                        font-weight: bold;
                        margin: 10px 0 5px 0;
                    }
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        margin: 10px 0;
                    }
                    td, th {
                        border: 1px solid #000;
                        padding: 5px;
                        font-family: 'Times New Roman', serif;
                        font-size: 11pt;
                    }
                    th {
                        background-color: #f0f0f0;
                        font-weight: bold;
                    }
                    strong, b { 
                        font-weight: bold;
                    }
                    em, i { 
                        font-style: italic;
                    }
                    u { 
                        text-decoration: underline;
                    }
                    img { 
                        max-width: 100%; 
                        height: auto; 
                        margin: 5px 0;
                        display: block;
                    }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .text-left { text-align: left; }
                    
                    @page {
                        margin: 20mm;
                        size: A4 portrait;
                        mso-page-orientation: portrait;
                    }
                    
                    /* Ensure no extra spacing */
                    section, article, div {
                        margin: 0;
                        padding: 0;
                    }
                </style>
            </head>
            <body>
                ${previewHTML}
            </body>
            </html>
        `;
        
        // Create blob and download
        const blob = new Blob(['\ufeff', htmlContent], { type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('downloadFormatModal'));
        if(modal) modal.hide();
    }

    function sanitizeFilename(filename) {
        // Remove special characters dan replace spaces with underscore
        return filename
            .toLowerCase()
            .replace(/[^a-z0-9]/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_|_$/g, '');
    }

</script>

<!-- HTML2PDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

@endsection


