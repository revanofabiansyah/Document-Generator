@extends('layouts.app')

@section('content')

<section class="py-5">
    <div class="container px-5">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 fw-bolder">DocGen Dashboard</h1>
                <p class="text-muted mb-0">Selamat datang, <strong>{{ $user->name }}</strong></p>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>

        <!-- Section 1: Choose a template -->
        <div class="mb-5">
            <h5 class="fw-bold mb-4">Pilih Template Dokumen</h5>
            <div class="row g-4">
                @forelse($documents as $document)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm hover-card">
                            <div class="card-body text-center">
                                <div class="mb-3" style="font-size: 48px;">
                                    <i class="bi bi-file-earmark-text text-primary"></i>
                                </div>
                                <h6 class="card-title fw-bold">{{ $document->title }}</h6>
                                @if($document->description)
                                    <p class="card-text small text-muted">{{ Str::limit($document->description, 50) }}</p>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-top">
                                <form action="{{ route('documents.user.start', ['user' => $user->name, 'templateId' => $document->id]) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-pencil me-1"></i>Mulai
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Belum ada template yang dipublikasikan oleh admin.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Section 2: Document History/In Progress -->
        <div class="mb-5">
            <h5 class="fw-bold mb-4">Dokumen Saya</h5>
            @if($userDocuments->count() > 0)
                <div class="row g-4">
                    @foreach($userDocuments as $userDoc)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm hover-card">
                                <div class="card-body">
                                    <div class="mb-3" style="font-size: 48px;">
                                        <i class="bi bi-file-earmark-check text-success"></i>
                                    </div>
                                    <h6 class="card-title fw-bold">{{ $userDoc->title }}</h6>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            <i class="bi bi-clock me-1"></i>
                                            Diubah: {{ $userDoc->updated_at->format('d M Y H:i') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-stairs me-1"></i>
                                            Step: {{ $userDoc->current_step }}/5
                                        </small>
                                    </div>
                                    @if($userDoc->current_step == 5)
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-warning">Dalam Proses</span>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent border-top d-flex gap-2">
                                    <a href="{{ route('documents.user.fill', ['user' => $user->name, 'document' => $userDoc, 'step' => $userDoc->current_step]) }}" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="bi bi-pencil me-1"></i>Lanjut
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $userDoc->id }}">
                                        <i class="bi bi-trash me-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $userDoc->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Hapus Dokumen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus dokumen "<strong>{{ $userDoc->title }}</strong>"? Tindakan ini tidak dapat dibatalkan.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('documents.user.delete', ['user' => $user->name, 'document' => $userDoc]) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada dokumen yang dibuat. Mulai membuat dokumen baru dengan memilih template di atas.
                </div>
            @endif
        </div>
    </div>
</section>

<style>
    .hover-card {
        transition: all 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
</style>

@endsection
