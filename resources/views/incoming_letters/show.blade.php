@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">📄 Incoming Letter Details</h4>
            <a href="{{ route('incoming-letters.index') }}" class="btn btn-light btn-sm fw-bold">← Back</a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p><strong>Number:</strong> {{ $incomingLetter->number ?? '—' }}</p>
                    <p><strong>Date:</strong> {{ $incomingLetter->date ? \Carbon\Carbon::parse($incomingLetter->date)->format('d/m/Y') : '—' }}</p>
                    <p><strong>Reference:</strong> {{ $incomingLetter->reference ?? '—' }}</p>
                    <p><strong>Annex:</strong> {{ $incomingLetter->annex ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Company:</strong> {{ $incomingLetter->company }}</p>
                    <p><strong>Addressed To:</strong> {{ $incomingLetter->addressed_to ?? '—' }}</p>
                    <p><strong>Subject:</strong> {{ $incomingLetter->subject }}</p>
                    <p>
                        <strong>Status:</strong>
                        @if($incomingLetter->status == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($incomingLetter->status == 'in-progress')
                            <span class="badge bg-info text-dark">In Progress</span>
                        @elseif($incomingLetter->status == 'viewed')
                            <span class="badge bg-secondary">Viewed</span>
                        @elseif($incomingLetter->status == 'responded')
                            <span class="badge bg-success">Responded</span>
                        @elseif($incomingLetter->status == 'done')
                            <span class="badge bg-dark">Done</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-3">
                <p><strong>Observation:</strong></p>
                <p class="text-muted">{{ $incomingLetter->observation ?? '—' }}</p>
            </div>

            <!-- Main Attachment -->
            @if($incomingLetter->attachment)
                <div class="mt-3">
                    <a href="{{ asset('storage/' . $incomingLetter->attachment) }}" target="_blank" class="btn btn-outline-primary">
                        📂 View Attachment
                    </a>
                </div>
            @endif

            <!-- Annexes -->
            @if(!empty($annexes) && $annexes->count() > 0)
                <div class="mt-3">
                    <strong>Annexes:</strong>
                    <ul class="list-group mt-1">
                        @foreach($annexes as $annex)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ asset('storage/' . $annex->file_path) }}" target="_blank">
                                    📄 {{ $annex->filename }}
                                </a>
                                <form action="{{ route('incoming-letters.annexes.destroy', $annex->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this annex?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('incoming-letters.print', $incomingLetter->id) }}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print
                </a>
                <a href="{{ route('incoming-letters.email.form', $incomingLetter->id) }}" class="btn btn-success">
                    <i class="fas fa-envelope"></i> Email
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
