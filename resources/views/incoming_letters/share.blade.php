@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Share Letter</h2>

    <div class="card p-4">
        <h5 class="mb-3">Letter: {{ $incomingLetter->subject }}</h5>
        <p><strong>Reference:</strong> {{ $incomingLetter->reference ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $incomingLetter->date?->format('Y-m-d') ?? 'N/A' }}</p>

        <div class="d-flex gap-3 mt-3">
            <!-- Web Share API -->
            <button onclick="shareLetter()" class="btn btn-primary">
                <i class="fas fa-share-alt"></i> Share
            </button>

            <!-- Download attachment -->
            @if($incomingLetter->attachment)
                <a href="{{ asset('storage/' . $incomingLetter->attachment) }}" target="_blank" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Download Attachment
                </a>
            @endif

            <a href="{{ route('incoming-letters.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>

<script>
function shareLetter() {
    if (navigator.share) {
        navigator.share({
            title: 'Courier Management KPS',
            text: 'Check out this letter: {{ $incomingLetter->subject }}',
            url: window.location.href
        })
        .then(() => console.log('Shared successfully'))
        .catch((error) => console.log('Error sharing', error));
    } else {
        alert('Share feature is not supported on this browser.');
    }
}
</script>
@endsection
