@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">✏️ Modifier un courrier sortant</h3>
        </div>
        <div class="card-body">
            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('outgoing-letters.update', $letter->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Letter Info Section -->
                <div class="mb-4">
                    <h5 class="fw-bold text-secondary mb-3">📌 Letter Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">N° <span class="text-danger">*</span></label>
                            <input type="text" name="number" value="{{ old('number', $letter->number) }}"
                                   class="form-control @error('number') is-invalid @enderror"
                                   placeholder="Enter letter number" required>
                            @error('number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{ old('date', $letter->date) }}"
                                   class="form-control @error('date') is-invalid @enderror" required>
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Destinataire <span class="text-danger">*</span></label>
                            <input type="text" name="recipient" value="{{ old('recipient', $letter->recipient) }}"
                                   class="form-control @error('recipient') is-invalid @enderror"
                                   placeholder="Enter recipient name" required>
                            @error('recipient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Objet <span class="text-danger">*</span></label>
                            <input type="text" name="subject" value="{{ old('subject', $letter->subject) }}"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   placeholder="Enter subject" required>
                            @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Info Section -->
                <div class="mb-4">
                    <h5 class="fw-bold text-secondary mb-3">📝 Additional Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Observation</label>
                            <textarea name="observation" class="form-control" rows="3"
                                      placeholder="Any notes or comments">{{ old('observation', $letter->observation) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pièce jointe</label>
                            <input type="file" name="attachment" class="form-control">
                            @if($letter->attachment)
                                <small class="d-block mt-1">Fichier actuel :
                                    <a href="{{ asset('storage/' . $letter->attachment) }}" target="_blank" class="text-decoration-underline">
                                        {{ basename($letter->attachment) }}
                                    </a>
                                </small>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status', $letter->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="sent" {{ old('status', $letter->status) == 'sent' ? 'selected' : '' }}>Envoyé</option>
                                <option value="archived" {{ old('status', $letter->status) == 'archived' ? 'selected' : '' }}>Archivé</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('outgoing-letters.index') }}" class="btn btn-secondary px-4">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

