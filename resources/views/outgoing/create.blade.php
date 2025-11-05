@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">➕ Nouveau Courrier Sortant</h3>
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

            <form action="{{ route('outgoing-letters.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Letter Info Section -->
                <div class="mb-4">
                    <h5 class="fw-bold text-secondary mb-3">📌 Informations du courrier</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{ old('date') }}"
                                   class="form-control @error('date') is-invalid @enderror" required>
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Référence</label>
                            <input type="text" name="reference" value="{{ old('reference') }}"
                                   class="form-control @error('reference') is-invalid @enderror"
                                   placeholder="Entrez la référence">
                            @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Destinataire <span class="text-danger">*</span></label>
                            <input type="text" name="recipient" value="{{ old('recipient') }}"
                                   class="form-control @error('recipient') is-invalid @enderror"
                                   placeholder="Entrez le destinataire" required>
                            @error('recipient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Adressé à</label>
                            <input type="text" name="addressed_to" value="{{ old('addressed_to') }}"
                                   class="form-control @error('addressed_to') is-invalid @enderror"
                                   placeholder="Entrez à qui c’est adressé">
                            @error('addressed_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Objet <span class="text-danger">*</span></label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   placeholder="Entrez l’objet" required>
                            @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mb-4">
                    <h5 class="fw-bold text-secondary mb-3">📝 Informations supplémentaires</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Observation</label>
                            <textarea name="observation" class="form-control" rows="3"
                                      placeholder="Commentaires ou remarques">{{ old('observation') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pièce jointe</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Envoyé</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archivé</option>
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
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
