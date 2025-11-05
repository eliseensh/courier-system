<div class="row g-3">
    <!-- Date -->
    <div class="col-md-6">
        <label class="form-label">Date</label>
        <input type="date" name="date"
            value="{{ old('date', isset($incomingLetter) && $incomingLetter->date ? $incomingLetter->date->format('Y-m-d') : '') }}"
            class="form-control @error('date') is-invalid @enderror" required>
        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <!-- Reference -->
    <div class="col-md-6">
        <label class="form-label">Reference</label>
        <input type="text" name="reference" value="{{ old('reference', $incomingLetter->reference ?? '') }}"
            class="form-control">
    </div>

    <!-- Company -->
    <div class="col-md-6">
        <label class="form-label">Company</label>
        <input type="text" name="company" value="{{ old('company', $incomingLetter->company ?? '') }}"
            class="form-control @error('company') is-invalid @enderror" required>
        @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <!-- Addressed To -->
    <div class="col-md-6">
        <label class="form-label">Addressed To</label>
        <input type="text" name="addressed_to" value="{{ old('addressed_to', $incomingLetter->addressed_to ?? '') }}"
            class="form-control">
    </div>

    <!-- Subject -->
    <div class="col-md-12">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" value="{{ old('subject', $incomingLetter->subject ?? '') }}"
            class="form-control @error('subject') is-invalid @enderror" required>
        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <!-- Status -->
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(['pending' => 'Pending', 'in-progress' => 'In Progress', 'viewed' => 'Viewed', 'responded' => 'Responded', 'done' => 'Done'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $incomingLetter->status ?? '') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <!-- Observation -->
    <div class="col-md-6">
        <label class="form-label">Observation</label>
        <textarea name="observation" class="form-control" rows="2">{{ old('observation', $incomingLetter->observation ?? '') }}</textarea>
    </div>

    <!-- Main Attachment -->
    <div class="col-md-12 mb-3">
        <label class="form-label">Main Attachment</label>
        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
        @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror

        @if(!empty($incomingLetter->attachment))
            <div class="mt-2">
                <a href="{{ asset('storage/' . $incomingLetter->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> View Attachment
                </a>
            </div>
        @endif
    </div>

    <!-- Annex Section -->
    <div class="col-md-12">
        <label class="form-label">Annexes</label>
        <div id="annexes-container">
            <div class="input-group mb-2 annex-item">
                <input type="file" name="annexes[]" class="form-control" multiple>
                <button type="button" class="btn btn-success add-annex">+</button>
            </div>
        </div>
        <small class="text-muted">You can upload multiple annex files.</small>

        @if(isset($incomingLetter) && $incomingLetter->annexes->count() > 0)
            <ul class="list-group mt-2">
                @foreach($incomingLetter->annexes as $annex)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ asset('storage/' . $annex->file_path) }}" target="_blank">📄 {{ $annex->filename }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<!-- Add Annex Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('annexes-container');

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-annex')) {
            const newInput = document.createElement('div');
            newInput.classList.add('input-group', 'mb-2', 'annex-item');
            newInput.innerHTML = `
                <input type="file" name="annexes[]" class="form-control">
                <button type="button" class="btn btn-danger remove-annex">−</button>
            `;
            container.appendChild(newInput);
        }

        if (e.target.classList.contains('remove-annex')) {
            e.target.closest('.annex-item').remove();
        }
    });
});
</script>
