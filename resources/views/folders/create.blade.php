@extends('layouts.app')

@section('content')
    <h1>Create Folder</h1>
    <form action="{{ route('folders.store', $folder) }}" method="POST">
        @csrf
        <div>
            <label for="name">Folder Name</label>
            <input type="text" name="name" id="name" required>
        </div>
        <button type="submit">Create Folder</button>
    </form>
@endsection
