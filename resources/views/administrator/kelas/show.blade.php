@extends('administrator.layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Detail Kelas: {{ $kelas->nama }}</h2>
            <div>
                <a href="{{ route('admin.jurusan.show', $kelas->jurusan_id) }}" class="btn btn-secondary">Back</a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    Import Siswa
                </button>
            </div>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <strong>Informasi Kelas</strong>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Nama:</strong> {{ $kelas->nama }}</p>
                <p class="mb-1"><strong>Jurusan:</strong> {{ $kelas->jurusan ? $kelas->jurusan->nama : 'N/A' }}</p>
                <p class="mb-0"><strong>Total Siswa:</strong> {{ $kelas->students->count() }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>Daftar Siswa</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas->students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->nisn }}</td>
                                <td>{{ $student->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.kelas.import', $kelas->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="importModalLabel">Import Siswa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label for="file" class="form-label">Upload File Excel (.xlsx, .xls, .csv)</label>
                <input class="form-control" type="file" id="file" name="file" required accept=".xlsx, .xls, .csv">
            </div>
            <div class="alert alert-info py-2 mb-0">
                <strong>Format Kolom Excel:</strong><br>
                A: NISN<br>
                B: Name (Nama Siswa)
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Import Data</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
