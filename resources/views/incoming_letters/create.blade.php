@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Add Incoming Letter</h1>

    <form action="{{ route('incoming-letters.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Include reusable form fields --}}
        @include('incoming_letters._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('incoming-letters.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
