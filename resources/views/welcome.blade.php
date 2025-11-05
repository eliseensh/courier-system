@extends('layouts.app')

@section('content')
@php
    $totalIncoming = $incomingLetters->count();
    $totalOutgoing = $outgoingLetters->count();
@endphp

<!-- Elegant Animated Gradient Background -->
<style>
    .animated-bg {
        position: relative;
        background: linear-gradient(120deg, #1e3c72, #2a5298, #e0c36f);
        background-size: 200% 200%;
        animation: gradientShift 10s ease infinite;
        border-radius: 20px;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 1s ease forwards;
    }
    .fade-delay-1 { animation-delay: 0.3s; }
    .fade-delay-2 { animation-delay: 0.6s; }
    .fade-delay-3 { animation-delay: 0.9s; }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.1; /* subtle watermark */
        pointer-events: none;
        max-width: 70%;
        max-height: 70%;
        z-index: 0;
    }
</style>

<!-- Hero Section -->
<div class="welcome-bg animated-bg d-flex align-items-center justify-content-center mb-5 position-relative" 
     style="min-height: 85vh; overflow: hidden; box-shadow: 0 0 25px rgba(0,0,0,0.25);">

    <!-- Watermark Image -->
    <img src="{{ asset('images/your-picture.jpg') }}" alt="Hero Watermark" class="hero-watermark">

    <div class="text-center p-5 bg-dark bg-opacity-50 rounded-4 shadow-lg w-75 fade-in fade-delay-1" style="position: relative; z-index: 1;">
        <h1 class="display-5 fw-bold text-white mb-3">📬 Welcome to Courier Management – KPS LOG SA</h1>
        <p class="lead text-light mb-4">
            Manage, track, and send your incoming and outgoing letters efficiently and securely.
        </p>

        <!-- Main Buttons -->
        <div class="d-flex flex-wrap justify-content-center gap-3 fade-in fade-delay-2">
            <a href="{{ route('incoming-letters.index') }}" 
               class="btn btn-lg btn-success shadow-sm px-4 py-2">
                📥 Incoming Letters ({{ $totalIncoming }})
            </a>
            <a href="{{ route('outgoing-letters.index') }}" 
               class="btn btn-lg btn-primary shadow-sm px-4 py-2">
                📤 Outgoing Letters ({{ $totalOutgoing }})
            </a>
            @guest
                <a href="{{ route('login') }}" 
                   class="btn btn-lg btn-warning text-dark fw-bold shadow-sm px-4 py-2">
                    🔐 Login
                </a>
            @else
                <a href="{{ route('dashboard') }}" 
                   class="btn btn-lg btn-info text-dark fw-bold shadow-sm px-4 py-2">
                    🧭 Go to Dashboard
                </a>
            @endguest
        </div>
    </div>
</div>

<!-- Quick Summary Section -->
<div class="container text-center mt-4">
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4 fade-in fade-delay-2">
            <div class="card border-0 shadow-lg rounded-4 h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-envelope-paper-fill text-success display-5"></i>
                    </div>
                    <h5 class="fw-semibold text-muted">Incoming Letters</h5>
                    <p class="display-4 fw-bold text-success mt-2">{{ $totalIncoming }}</p>
                    <a href="{{ route('incoming-letters.index') }}" class="btn btn-outline-success btn-sm rounded-pill mt-2">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 fade-in fade-delay-3">
            <div class="card border-0 shadow-lg rounded-4 h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-send-fill text-primary display-5"></i>
                    </div>
                    <h5 class="fw-semibold text-muted">Outgoing Letters</h5>
                    <p class="display-4 fw-bold text-primary mt-2">{{ $totalOutgoing }}</p>
                    <a href="{{ route('outgoing-letters.index') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="text-center mt-5 text-muted small fade-in fade-delay-3">
    <hr class="w-25 mx-auto mb-3">
    <p>© {{ date('Y') }} KPS LOG SA. Courier Management System — All rights reserved/ Made by Elisee Nshombo</p>
</div>
@endsection
