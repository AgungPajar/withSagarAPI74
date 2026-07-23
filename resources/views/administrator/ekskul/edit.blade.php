@extends('administrator.layouts.app')

@section('title', 'Edit Ekskul')

@section('content')
<div class="row mt-4">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <strong>Edit Ekstrakurikuler</strong>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('admin.ekskul.update', $ekskul->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Ekskul</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $ekskul->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Admin Klub (Pilih Siswa)</label>
                        <select class="form-select" id="student_id" name="student_id">
                            <option value="">-- Tidak Ada Admin --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id', $ekskul->student_id) == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} (NISN: {{ $student->nisn }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Siswa ini akan menjadi Admin untuk mengelola fitur ekskul ini.</small>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $ekskul->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="group_link" class="form-label">Link Grup (WhatsApp/Line)</label>
                        <input type="url" class="form-control" id="group_link" name="group_link" value="{{ old('group_link', $ekskul->group_link) }}">
                    </div>
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo Ekskul</label>
                        @if($ekskul->logo_url)
                            <div class="mb-2">
                                <img src="{{ $ekskul->logo_url }}" alt="Logo" width="100" class="img-thumbnail">
                            </div>
                        @endif
                        <input class="form-control" type="file" id="logo" name="logo" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah logo.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.ekskul.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
