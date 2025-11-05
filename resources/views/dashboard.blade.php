@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-primary">📬 Courier System — Dashboard</h2>
        <p class="text-muted">Overview of incoming & outgoing letters</p>
    </div>

    <!-- Incoming Letters Stats -->
    <h4 class="text-secondary mb-3">📥 Incoming Letters</h4>
    <div class="row mb-5">
        @foreach($incomingStats as $status => $count)
        <div class="col-md-2 col-sm-4 mb-3">
            <a href="{{ route('incoming-letters.index', ['status' => $status]) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-0 p-3 rounded-4 hover-shadow h-100">
                    <div class="fs-1 mb-2">
                        @switch($status)
                            @case('pending') ⏳ @break
                            @case('in-progress') 🔄 @break
                            @case('viewed') 👁️ @break
                            @case('responded') 📩 @break
                            @case('done') ✅ @break
                            @case('archived') 🗂️ @break
                            @default 📄
                        @endswitch
                    </div>
                    <h6 class="fw-semibold text-capitalize">{{ str_replace('_',' ', $status) }}</h6>
                    <p class="fs-4 fw-bold text-primary">{{ $count }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Outgoing Letters Stats -->
    <h4 class="text-secondary mb-3">📤 Outgoing Letters</h4>
    <div class="row mb-5">
        @foreach($outgoingStats as $status => $count)
        <div class="col-md-2 col-sm-4 mb-3">
            <a href="{{ route('outgoing-letters.index', ['status' => $status]) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-0 p-3 rounded-4 hover-shadow h-100">
                    <div class="fs-1 mb-2">
                        @switch($status)
                            @case('draft') 📝 @break
                            @case('sent') ✉️ @break
                            @case('archived') 🗂️ @break
                            @default 📄
                        @endswitch
                    </div>
                    <h6 class="fw-semibold text-capitalize">{{ str_replace('_',' ', $status) }}</h6>
                    <p class="fs-4 fw-bold text-success">{{ $count }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Charts -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-3 rounded-4">
                <h5 class="fw-bold mb-3">📥 Incoming Letters (Last 12 months)</h5>
                <canvas id="incomingChart"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-3 rounded-4">
                <h5 class="fw-bold mb-3">📤 Outgoing Letters (Last 12 months)</h5>
                <canvas id="outgoingChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const months = @json($months);
const incomingData = @json($incomingMonthly);
const outgoingData = @json($outgoingMonthly);

new Chart(document.getElementById('incomingChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Incoming Letters',
            data: incomingData,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.2)',
            tension: 0.3,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, precision:0 },
        }
    }
});

new Chart(document.getElementById('outgoingChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Outgoing Letters',
            data: outgoingData,
            borderColor: '#198754',
            backgroundColor: 'rgba(25,135,84,0.2)',
            tension: 0.3,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, precision:0 },
        }
    }
});
</script>
@endsection
