@extends('layouts.admin')

@section('content')

<style>
    .preview-section {
        display: flex;
        flex-direction: column;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .editor-header {
        background: #0d6efd;
        color: white;
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 14px;
    }
    
    .preview-content {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
        max-height: 800px;
        background: white;
        font-family: 'Times New Roman', serif;
        line-height: 1.8;
        border: 5px solid #f0f0f0;
        margin: 15px;
    }
</style>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="h4 mb-0">Edit Document: <strong>{{ $document->title }}</strong></h2>
        <div class="mt-2">
            @if($document->is_published)
                <span class="badge bg-success">
                    <i class="fas fa-check-circle me-1"></i>Published
                </span>
            @else
                <span class="badge bg-warning">
                    <i class="fas fa-hourglass-half me-1"></i>Draft
                </span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('documents.input') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        @if(!$document->is_published)
            <form action="{{ route('documents.publish', $document) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-paper-plane me-2"></i>Publish
                </button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="preview-section">
    <div class="editor-header">
        <i class="fas fa-eye me-2"></i>Preview Document Layout
    </div>
    <div class="preview-content" id="preview">
        <p style="color: #999; text-align: center;">Preview akan muncul di sini setelah dokumen dipublikasikan...</p>
    </div>
</div>


@endsection

