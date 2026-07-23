@extends('administrator.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row mt-4">
    <div class="col-md-12 mb-4">
        <h2>Welcome to Admin Dashboard</h2>
        <p class="text-muted">You are logged in as {{ auth('admin')->user()->name }}.</p>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Jurusan</h5>
                <p class="card-text fs-2">{{ $stats['jurusan'] ?? 0 }}</p>
            </div>
            <div class="card-footer bg-transparent border-white">
                <a href="{{ route('admin.jurusan.index') }}" class="text-white text-decoration-none">Manage Jurusan &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Kelas</h5>
                <p class="card-text fs-2">{{ $stats['kelas'] ?? 0 }}</p>
            </div>
            <div class="card-footer bg-transparent border-white">
                <a href="{{ route('admin.kelas.index') }}" class="text-white text-decoration-none">Manage Kelas &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Siswa</h5>
                <p class="card-text fs-2">{{ $stats['siswa'] ?? 0 }}</p>
            </div>
            <div class="card-footer bg-transparent border-white">
                <a href="{{ route('admin.siswa.index') }}" class="text-white text-decoration-none">Manage Siswa &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection
