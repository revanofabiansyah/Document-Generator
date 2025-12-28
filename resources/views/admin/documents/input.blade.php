@extends('layouts.admin')

@section('content')

<div class="mb-4">
    <h2 class="h4 font-weight-bold text-gray-800">Input Document</h2>
</div>

<!-- Alert Messages -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terjadi Kesalahan!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Add Document Card -->
<div class="card mb-4">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-plus-circle me-2"></i>Add Document
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('documents.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Judul Dokumen</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                        id="title" name="title" placeholder="Masukkan judul dokumen..." 
                        required value="{{ old('title') }}">
                    @error('title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="template_type" class="form-label">Tipe Template</label>
                    <select class="form-select @error('template_type') is-invalid @enderror" 
                        id="template_type" name="template_type" required>
                        <option value="">--Choose--</option>
                        <option value="surat_undangan" {{ old('template_type') == 'surat_undangan' ? 'selected' : '' }}>
                            Surat Undangan
                        </option>
                        <option value="surat_pengumuman" {{ old('template_type') == 'surat_pengumuman' ? 'selected' : '' }}>
                            Surat Pengumuman
                        </option>
                        <option value="surat_keterangan" {{ old('template_type') == 'surat_keterangan' ? 'selected' : '' }}>
                            Surat Keterangan
                        </option>
                    </select>
                    @error('template_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-12 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Document List Card -->
<div class="card">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-file-alt me-2"></i>Document List
        </h6>
    </div>
    <div class="card-body p-0">
        @if($documents->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <tbody>
                        @foreach($documents as $document)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <i class="fas fa-file-document me-2 text-primary"></i>
                                            <strong>{{ $document->title }}</strong>
                                            <div style="font-size: 12px; color: #999; margin-top: 3px;">
                                                {{ ucfirst(str_replace('_', ' ', $document->template_type)) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 150px; text-align: right;">
                                    <button type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editNameModal"
                                        onclick="setEditDocument({{ $document->id }}, '{{ $document->title }}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('documents.destroy', $document) }}" 
                                        method="POST" style="display:inline;" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Document">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3" style="display: block; margin-bottom: 15px;"></i>
                <p class="text-muted mb-3">Belum ada dokumen</p>
                <p class="text-muted" style="font-size: 13px;">Buat dokumen baru dengan mengisi form di atas</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Edit Document Name -->
<div class="modal fade" id="editNameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Nama Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editNameForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="documentTitle" class="form-label">Judul Dokumen</label>
                        <input type="text" class="form-control" id="documentTitle" name="title" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setEditDocument(documentId, title) {
    document.getElementById('documentTitle').value = title;
    document.getElementById('editNameForm').action = '/documents/' + documentId + '/update-name';
}
</script>

@endsection

