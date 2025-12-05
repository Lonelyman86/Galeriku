@extends('main')

@section('content')
<style>
    .chart-wrapper {
    max-width: 300px;   /* ukuran donat */
    max-height: 300px;
    margin: 0 auto;
}

.chart-wrapper canvas {
    width: 100% !important;
    height: 100% !important;
}

</style>

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-speedometer2 text-danger me-2"></i> Dashboard Admin
        </h2>
        <span class="badge bg-danger fs-6 px-3 py-2">Admin Panel</span>
    </div>

    {{-- Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-2"></i>
                    <h6 class="mt-2">Total User</h6>
                    <h3 class="fw-bold">{{ $totalUser }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-folder-fill fs-2"></i>
                    <h6 class="mt-2">Total Album</h6>
                    <h3 class="fw-bold">{{ $totalAlbum }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-image-fill fs-2"></i>
                    <h6 class="mt-2">Total Foto</h6>
                    <h3 class="fw-bold">{{ $totalFoto }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="bi bi-chat-left-text-fill fs-2"></i>
                    <h6 class="mt-2">Total Komentar</h6>
                    <h3 class="fw-bold">{{ $totalKomentar }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Foto --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-camera me-2 text-danger"></i>Status Foto</h5>
            <div class="row text-center">
                <div class="col-md-4">
                    <h2 class="fw-bold text-warning">{{ $fotoPending }}</h2>
                    <p class="text-muted mb-0">Pending</p>
                </div>
                <div class="col-md-4">
                    <h2 class="fw-bold text-success">{{ $fotoApproved }}</h2>
                    <p class="text-muted mb-0">Approved</p>
                </div>
                <div class="col-md-4">
                    <h2 class="fw-bold text-danger">{{ $fotoRejected }}</h2>
                    <p class="text-muted mb-0">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
<div class="card border-0 shadow-sm">
    <div class="card-body text-center">

        <h5 class="fw-bold mb-4">
            <i class="bi bi-graph-up-arrow text-danger me-2"></i>Grafik Status Foto
        </h5>

        <div class="chart-wrapper">
            <canvas id="fotoChart"></canvas>
        </div>

    </div>
</div>
</div>

{{-- Custom Hover Style --}}
<style>
    .hover-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
</style>

{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('fotoChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                data: @json($chartData['data']),
                backgroundColor: ['#FACC15', '#22C55E', '#EF4444'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { font: { size: 14 } }
                }
            }
        }
    });
</script>
@endsection
