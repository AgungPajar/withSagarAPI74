@extends('administrator.layouts.app')

@section('title', 'Manage Jurusan')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Jurusan</h2>
            <a href="{{ route('admin.jurusan.create') }}" class="btn btn-primary">Create New Jurusan</a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No</th>
                            <th>Urutan</th>
                            <th>Nama</th>
                            <th>Singkatan</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-jurusans">
                        @forelse($jurusans as $index => $jurusan)
                            <tr data-id="{{ $jurusan->id }}">
                                <td><span class="handle" style="cursor: grab; font-size: 1.2rem;">☰</span></td>
                                <td>{{ $jurusans->firstItem() + $index }}</td>
                                <td>{{ $jurusan->urutan }}</td>
                                <td>{{ $jurusan->nama }}</td>
                                <td>{{ $jurusan->singkatan }}</td>
                                <td>{{ $jurusan->slug }}</td>
                                <td>
                                    <a href="{{ route('admin.jurusan.show', $jurusan->id) }}" class="btn btn-sm btn-info text-white">Detail</a>
                                    <a href="{{ route('admin.jurusan.edit', $jurusan->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.jurusan.destroy', $jurusan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No Jurusan found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $jurusans->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('sortable-jurusans');
        if (el) {
            Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function (evt) {
                    var order = [];
                    el.querySelectorAll('tr').forEach(function(row) {
                        var id = row.getAttribute('data-id');
                        if (id) order.push(id);
                    });

                    Swal.fire({
                        title: 'Mohon tunggu',
                        text: 'Sedang menyimpan urutan baru...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route('admin.jurusan.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message || 'Urutan berhasil diperbarui',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal memperbarui urutan.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem.'
                        });
                    });
                }
            });
        }
    });
</script>
@endpush
