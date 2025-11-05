@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Send Letter via Email</h2>

        <form action="{{ route('incoming-letters.email.send', $incomingLetter->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">To:</label>
                <input type="email" name="to" value="{{ old('to') }}" class="form-control @error('to') is-invalid @enderror"
                    required>
                @error('to') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Subject:</label>
                <input type="text" name="subject" value="{{ old('subject', $incomingLetter->subject) }}"
                    class="form-control @error('subject') is-invalid @enderror" required>
                @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Message:</label>
                <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror"
                    required>{{ old('message') }}</textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Attachment (optional):</label>
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror

                @if($incomingLetter->attachment)
                    <small class="text-muted">Current file:
                        <a href="{{ asset('storage/' . $incomingLetter->attachment) }}" target="_blank">
                            {{ basename($incomingLetter->attachment) }}
                        </a>
                    </small>
                @endif
            </div>

            <button type="submit" class="btn btn-success">Send Email</button>
            <a href="{{ route('incoming-letters.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection