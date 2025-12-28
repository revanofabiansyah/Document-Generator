@extends('layouts.admin')

@section('content')

<!-- Dashboard Header -->
<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Welcome Admin !</h1>
            <p class="text-muted small mt-2">Manage your documents efficiently</p>
        </div>
        <div>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-download me-2"></i>Generate Report
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="text-primary text-uppercase small mb-2">Total Documents</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalDocuments ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="text-success text-uppercase small mb-2">Published Documents</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $publishedDocuments ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="text-info text-uppercase small mb-2">Total Users</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="text-warning text-uppercase small mb-2">Admin Users</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $adminUsers ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary">
                <h6 class="m-0 font-weight-bold text-white">Documents Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="documentsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary">
                <h6 class="m-0 font-weight-bold text-white">Users Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Documents Chart
    const documentsCtx = document.getElementById('documentsChart');
    if (documentsCtx) {
        new Chart(documentsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Published', 'Draft'],
                datasets: [{
                    data: [{{ $publishedDocuments ?? 0 }}, {{ ($totalDocuments ?? 0) - ($publishedDocuments ?? 0) }}],
                    backgroundColor: [
                        '#198754',
                        '#6c757d'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Users Chart
    const usersCtx = document.getElementById('usersChart');
    if (usersCtx) {
        new Chart(usersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Admin', 'Regular User'],
                datasets: [{
                    data: [{{ $adminUsers ?? 0 }}, {{ ($totalUsers ?? 0) - ($adminUsers ?? 0) }}],
                    backgroundColor: [
                        '#0d6efd',
                        '#6c757d'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .border-left-primary {
        border-left: 4px solid #0d6efd !important;
    }
    
    .border-left-success {
        border-left: 4px solid #198754 !important;
    }
    
    .border-left-info {
        border-left: 4px solid #0dcaf0 !important;
    }
    
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    
    .text-gray-800 {
        color: #333 !important;
    }
</style>

@endsection