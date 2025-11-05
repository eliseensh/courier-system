@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Title -->
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-primary">📬 Courier System — Incoming Letters History</h2>
        <p class="text-muted">View letters by date with full details</p>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">📂 Navigation</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('incoming-letters.index') }}" 
                       class="list-group-item list-group-item-action {{ request()->routeIs('incoming-letters.index') ? 'active' : '' }}">
                        📑 All Letters
                    </a>

                    <div class="list-group-item fw-bold bg-light">📅 By Year / Month / Day</div>

                    @foreach($years as $y)
                        <li class="list-unstyled px-2">
                            <details {{ (isset($year) && $year == $y) ? 'open' : '' }}>
                                <summary class="fw-semibold">{{ $y }}</summary>
                                <ul class="list-unstyled ms-3 mt-1">
                                    @for($m = 1; $m <= 12; $m++)
                                        @php
                                            $monthLetters = $letters->filter(function($l) use($y,$m){
                                                return $l->date && \Carbon\Carbon::parse($l->date)->year == $y &&
                                                       \Carbon\Carbon::parse($l->date)->month == $m;
                                            });
                                        @endphp
                                        @if($monthLetters->count() > 0)
                                            <li>
                                                <details {{ (isset($month) && $month == $m && isset($year) && $year == $y) ? 'open' : '' }}>
                                                    <summary class="text-primary">
                                                        {{ \Carbon\Carbon::create()->month($m)->locale('fr')->translatedFormat('F') }} ({{ $monthLetters->count() }})
                                                    </summary>
                                                    <ul class="list-unstyled ms-3 mt-1">
                                                        @foreach($monthLetters as $dayLetter)
                                                            @php
                                                                $date = $dayLetter->date ? \Carbon\Carbon::parse($dayLetter->date) : null;
                                                            @endphp
                                                            @if($date)
                                                                <li class="mb-1">
                                                                    <a href="{{ route('incoming-letters.show', $dayLetter->id) }}" 
                                                                       class="text-decoration-none">
                                                                        📄 {{ $date->format('d/m/Y') }} : 
                                                                        <span class="text-muted">{{ $dayLetter->subject ?? '—' }}</span>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li class="mb-1 text-danger">
                                                                    ⚠️ Invalid date for letter: {{ $dayLetter->subject ?? 'Unknown' }}
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            </li>
                                        @endif
                                    @endfor
                                </ul>
                            </details>
                        </li>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">📄 Incoming Letters History</h4>
                    <a href="{{ route('incoming-letters.index') }}" class="btn btn-light btn-sm fw-bold">← Back</a>
                </div>

                <div class="card-body p-0">
                    @if($letters->isEmpty())
                        <p class="text-center text-muted py-4">No letters found for the selected period.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered mb-0 align-middle text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 5%">N°</th>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 20%">Company</th>
                                        <th style="width: 25%">Subject</th>
                                        <th style="width: 15%">Status</th>
                                        <th style="width: 10%">Attachment</th>
                                        <th style="width: 15%">Annexes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($letters as $letter)
                                        @php
                                            $date = $letter->date ? \Carbon\Carbon::parse($letter->date) : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $letter->number ?? '—' }}</td>
                                            <td>{{ $date ? $date->format('d/m/Y') : '—' }}</td>
                                            <td class="text-start">{{ $letter->company }}</td>
                                            <td class="text-start">{{ $letter->subject }}</td>
                                            <td>
                                                @if($letter->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($letter->status == 'in-progress')
                                                    <span class="badge bg-info text-dark">In Progress</span>
                                                @elseif($letter->status == 'viewed')
                                                    <span class="badge bg-primary">Viewed</span>
                                                @elseif($letter->status == 'responded')
                                                    <span class="badge bg-success">Responded</span>
                                                @else
                                                    <span class="badge bg-secondary">Done</span>
                                                @endif
                                            </td>

                                            {{-- ✅ Attachment --}}
                                            <td>
                                                @if($letter->attachment)
                                                    <a href="{{ asset('storage/' . $letter->attachment) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary mb-1">
                                                        📂 View
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            {{-- ✅ Annexes --}}
                                            <td>
                                                @if($letter->annexes && $letter->annexes->count() > 0)
                                                    <div class="d-flex flex-column align-items-center">
                                                        @foreach($letter->annexes as $annex)
                                                            <a href="{{ asset('storage/' . $annex->file_path) }}" 
                                                               target="_blank" 
                                                               class="btn btn-sm btn-outline-secondary mb-1">
                                                                📄 Annex {{ $loop->iteration }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-outline-secondary {
    font-size: 0.85rem;
    padding: 4px 8px;
}
</style>
@endsection
