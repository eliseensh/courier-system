@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">🛠 Admin Dashboard</h2>
        <p class="text-muted">Overview of all incoming & outgoing letters</p>
    </div>

    <div class="row g-4">
        <!-- Incoming Letters Stats -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    📥 Incoming Letters ({{ $totalIncoming }})
                </div>
                <div class="card-body">
                    <p>Pending: {{ $pendingIncoming }}</p>
                    <p>Responded: {{ $respondedIncoming }}</p>
                    <p>Done: {{ $doneIncoming }}</p>
                </div>
            </div>
        </div>

        <!-- Outgoing Letters Stats -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    📤 Outgoing Letters ({{ $totalOutgoing }})
                </div>
                <div class="card-body">
                    <p>Draft: {{ $draftOutgoing }}</p>
                    <p>Sent: {{ $sentOutgoing }}</p>
                    <p>Archived: {{ $archivedOutgoing }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h5>All Letters</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Recipient / Subject</th>
                        <th>Status</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomingLetters as $letter)
                    <tr>
                        <td>{{ $letter->id }}</td>
                        <td>Incoming</td>
                        <td>{{ $letter->date ? \Carbon\Carbon::parse($letter->date)->format('d/m/Y') : '—' }}</td>
                        <td class="text-start">{{ $letter->subject }}</td>
                        <td>{{ ucfirst($letter->status) }}</td>
                        <td>
                            @if($letter->attachment)
                                <a href="{{ asset('storage/'.$letter->attachment) }}" target="_blank">View</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    @foreach($outgoingLetters as $letter)
                    <tr>
                        <td>{{ $letter->id }}</td>
                        <td>Outgoing</td>
                        <td>{{ $letter->date ? \Carbon\Carbon::parse($letter->date)->format('d/m/Y') : '—' }}</td>
                        <td class="text-start">{{ $letter->subject }}</td>
                        <td>{{ ucfirst($letter->status) }}</td>
                        <td>
                            @if($letter->attachment)
                                <a href="{{ asset('storage/'.$letter->attachment) }}" target="_blank">View</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
