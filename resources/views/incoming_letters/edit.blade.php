@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Incoming Letter</h1>

    <form action="{{ route('incoming-letters.update', $incomingLetter->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('incoming_letters._form')

        <button type="submit" class="btn btn-success mt-4">Update</button>
        <a href="{{ route('incoming-letters.index') }}" class="btn btn-secondary mt-4">Cancel</a>
    </form>
</div>
@endsection
