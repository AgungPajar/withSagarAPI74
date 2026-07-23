@extends('administrator.layouts.app')

@section('title', 'Create Kelas')

@section('content')
<div class="row mt-4">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h4>Create Kelas</h4>
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
                <form action="{{ route('admin.kelas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tingkatan" class="form-label">Tingkatan</label>
                        <select class="form-select" id="tingkatan" name="tingkatan" required>
                            <option value="">Select Tingkatan...</option>
                            <option value="X" {{ old('tingkatan') == 'X' ? 'selected' : '' }}>X</option>
                            <option value="XI" {{ old('tingkatan') == 'XI' ? 'selected' : '' }}>XI</option>
                            <option value="XII" {{ old('tingkatan') == 'XII' ? 'selected' : '' }}>XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="rombel" class="form-label">Rombel (Contoh: 1, 2, atau A, B)</label>
                        <input type="text" class="form-control" id="rombel" name="rombel" value="{{ old('rombel') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jurusan_id" class="form-label">Jurusan</label>
                        <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                            <option value="">Select Jurusan...</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
