@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Title -->
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-primary">📬 Courier System — Outgoing Letters</h2>
        <p class="text-muted">Manage, search, and track all your outgoing correspondence</p>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-primary text-white">
                    📂 Navigation
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('outgoing-letters.index') }}" 
                       class="list-group-item list-group-item-action {{ request()->routeIs('outgoing-letters.index') ? 'active' : '' }}">
                        📑 All Letters
                    </a>
                    <div class="list-group-item fw-bold bg-light">
                        📅 By Year / Month / Day
                    </div>

                    @foreach($years as $y)
                        <li class="list-unstyled px-2">
                            <details {{ (isset($year) && $year == $y) ? 'open' : '' }}>
                                <summary class="fw-semibold">{{ $y }}</summary>
                                <ul class="list-unstyled ms-3 mt-1">
                                    @for($m = 1; $m <= 12; $m++)
                                        @php
                                            $monthLetters = $outgoings->filter(function($l) use($y,$m){
                                                return \Carbon\Carbon::parse($l->date)->year == $y &&
                                                       \Carbon\Carbon::parse($l->date)->month == $m;
                                            });
                                        @endphp
                                        @if($monthLetters->count() > 0)
                                            <li>
                                                <details {{ (isset($month) && $month == $m && isset($year) && $year == $y) ? 'open' : '' }}>
                                                    <summary class="text-primary">
                                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }} ({{ $monthLetters->count() }})
                                                    </summary>
                                                    <ul class="list-unstyled ms-3 mt-1">
                                                        @foreach($monthLetters as $dayLetter)
                                                            <li class="mb-1">
                                                                <a href="{{ route('outgoing-letters.history', [
                                                                    'year' => \Carbon\Carbon::parse($dayLetter->date)->year,
                                                                    'month' => \Carbon\Carbon::parse($dayLetter->date)->month,
                                                                    'day' => \Carbon\Carbon::parse($dayLetter->date)->day
                                                                ]) }}" 
                                                                class="text-decoration-none">
                                                                    📄 {{ \Carbon\Carbon::parse($dayLetter->date)->format('d/m/Y') }} : 
                                                                    <span class="text-muted">{{ $dayLetter->subject }}</span>
                                                                </a>
                                                            </li>
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

        <!-- Main Table -->
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">📄 Outgoing Letters</h4>
                    <a href="{{ route('outgoing-letters.create') }}" class="btn btn-light btn-sm fw-bold">➕ New Letter</a>
                </div>
                <div class="card-body p-0">
                    @if($outgoings->isEmpty())
                        <p class="text-center text-muted py-4">No outgoing letters found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered mb-0 align-middle text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 5%">N°</th>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 15%">Reference</th>
                                        <th style="width: 15%">Recipient</th>
                                        <th style="width: 20%">Subject</th>
                                        <th style="width: 20%">Observation</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 10%">Attachment</th>
                                        <th style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($outgoings as $outgoing)
                                        <tr>
                                            <td class="fw-semibold">{{ $outgoing->number }}</td>
                                            <td>{{ \Carbon\Carbon::parse($outgoing->date)->format('d/m/Y') }}</td>
                                            <td>{{ $outgoing->reference ?? '—' }}</td>
                                            <td>{{ $outgoing->recipient }}</td>
                                            <td class="text-start">{{ $outgoing->subject }}</td>
                                            <td class="text-muted">{{ $outgoing->observation ?? '—' }}</td>
                                            <td>
                                                @if($outgoing->status == 'sent')
                                                    <span class="badge bg-success">Sent</span>
                                                @elseif($outgoing->status == 'draft')
                                                    <span class="badge bg-warning text-dark">Draft</span>
                                                @else
                                                    <span class="badge bg-secondary">Archived</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($outgoing->attachment)
                                                    <a href="{{ asset('storage/' . $outgoing->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        📂 View
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('outgoing-letters.edit', $outgoing->id) }}" 
                                                       class="btn btn-sm btn-outline-success">✏️</a>
                                                    <form action="{{ route('outgoing-letters.destroy', $outgoing->id) }}" 
                                                          method="POST" onsubmit="return confirm('Delete this letter?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                                    </form>
                                                </div>
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
@endsection
