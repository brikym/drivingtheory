@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Import otázek</h1>

        @if(session('success'))
            <div style="color: green">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="color: red">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="questions_file">Vyber JSON soubor:</label>
            <input type="file" name="questions_file" id="questions_file" required>
            <button type="submit">Nahrát</button>
        </form>
    </div>
@endsection


