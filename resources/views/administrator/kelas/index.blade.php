@extends('administrator.layouts.app')

@section('title', 'Manage Kelas')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Kelas</h2>
            <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">Create New Kelas</a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Jurusan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $index => $k)
                            <tr>
                                <td>{{ $kelas->firstItem() + $index }}</td>
                                <td>{{ $k->nama }}</td>
                                <td>{{ $k->slug }}</td>
                                <td>{{ $k->jurusan ? $k->jurusan->nama : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.kelas.show', $k->id) }}" class="btn btn-sm btn-info text-white">Detail</a>
                                    <button type="button" class="btn btn-sm btn-warning btn-edit" 
                                        data-id="{{ $k->id }}"
                                        data-tingkatan="{{ $k->tingkatan }}"
                                        data-jurusan="{{ $k->jurusan_id }}"
                                        data-rombel="{{ $k->rombel }}"
                                        data-nama="{{ $k->nama }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal">Edit</button>
                                    <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Kelas found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $kelas->links() }}
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tingkatan" class="form-label">Tingkatan</label>
                        <select class="form-select" id="edit_tingkatan" name="tingkatan" required>
                            <option value="">Select Tingkatan...</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_rombel" class="form-label">Rombel (Contoh: 1, 2, atau A, B)</label>
                        <input type="text" class="form-control" id="edit_rombel" name="rombel" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jurusan_id" class="form-label">Jurusan</label>
                        <select class="form-select" id="edit_jurusan_id" name="jurusan_id" required>
                            <option value="">Select Jurusan...</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" data-singkatan="{{ $jurusan->singkatan }}">
                                    {{ $jurusan->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        const editForm = document.getElementById('editForm');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const tingkatan = this.getAttribute('data-tingkatan');
                const jurusan_id = this.getAttribute('data-jurusan');
                const rombel = this.getAttribute('data-rombel');
                const nama = this.getAttribute('data-nama');
                
                editForm.action = `/admin/kelas/${id}`;
                document.getElementById('edit_tingkatan').value = tingkatan || '';
                document.getElementById('edit_jurusan_id').value = jurusan_id || '';
                document.getElementById('edit_rombel').value = rombel || '';
                document.getElementById('edit_nama').value = nama || '';
            });
        });

        function updateNamaKelas() {
            const tingkatan = document.getElementById('edit_tingkatan').value;
            const jurusanSelect = document.getElementById('edit_jurusan_id');
            const jurusanSingkatan = jurusanSelect.options[jurusanSelect.selectedIndex]?.getAttribute('data-singkatan') || '';
            const rombel = document.getElementById('edit_rombel').value;
            
            if (tingkatan && jurusanSingkatan && rombel) {
                document.getElementById('edit_nama').value = `${tingkatan} ${jurusanSingkatan} ${rombel}`;
            }
        }

        document.getElementById('edit_tingkatan').addEventListener('change', updateNamaKelas);
        document.getElementById('edit_jurusan_id').addEventListener('change', updateNamaKelas);
        document.getElementById('edit_rombel').addEventListener('input', updateNamaKelas);
    });
</script>
@endpush
