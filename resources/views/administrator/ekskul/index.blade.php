@extends('administrator.layouts.app')

@section('title', 'Manage Ekskul')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 style="font-size: 20px; font-weight: 800; color: #e2e8f0; margin: 0;">Manage Ekstrakurikuler</h1>
        <p style="color: #64748b; font-size: 12px; margin: 4px 0 0;">
            Total {{ $clubs->total() }} ekskul
            @if(request('search'))
                <span style="color: #818cf8;"> — hasil pencarian</span>
            @else
                &mdash; drag baris untuk ubah urutan
            @endif
        </p>
    </div>
    <a href="{{ route('admin.ekskul.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Tambah Ekskul
    </a>
</div>

{{-- Search Bar --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.ekskul.index') }}" id="searchForm">
            <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">
                        Cari Nama Ekskul / Admin
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: rgba(30,34,54,0.8); border-color: #1e2236; color: #64748b;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput" class="form-control"
                               placeholder="Cari..." value="{{ request('search') }}" autocomplete="off">
                        @if(request('search'))
                        <button type="button" id="clearSearch" class="btn btn-sm"
                                style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; border-left: none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.ekskul.index') }}" class="btn flex-fill"
                       style="background: rgba(100,108,255,0.1); color: #818cf8; border: 1px solid rgba(100,108,255,0.2);" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>#</th>
                        <th>Logo</th>
                        <th>Urutan</th>
                        <th>Nama Ekskul</th>
                        <th>Admin (Siswa)</th>
                        <th>Group Link</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="{{ request('search') ? '' : 'sortable-ekskul' }}">
                    @forelse($clubs as $index => $ekskul)
                        @php $keyword = request('search'); @endphp
                        <tr data-id="{{ $ekskul->id }}">
                            <td>
                                @if(!request('search'))
                                    <span class="handle" style="cursor: grab; color: #64748b; font-size: 1.1rem;">⠿</span>
                                @endif
                            </td>
                            <td style="color: #64748b;">{{ $clubs->firstItem() + $index }}</td>
                            <td>
                                @if($ekskul->logo_url)
                                    <img src="{{ $ekskul->logo_url }}" alt="Logo" width="40" height="40"
                                         class="rounded-circle" style="object-fit: cover; border: 2px solid rgba(100,108,255,0.3);">
                                @else
                                    <div style="width:40px;height:40px;background:rgba(100,108,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#818cf8;font-size:14px;font-weight:700;">
                                        {{ strtoupper(substr($ekskul->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 14px;">
                                <span class="badge" style="background: rgba(100,108,255,0.15); color: #818cf8; border-radius: 6px;">{{ $ekskul->urutan ?? 0 }}</span>
                            </td>
                            <td style="font-weight: 600;">
                                @if($keyword && stripos($ekskul->name, $keyword) !== false)
                                    {!! preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark style="background: rgba(100,108,255,0.3); color: #c7d2fe; border-radius: 3px; padding: 0 2px;">$1</mark>', e($ekskul->name)) !!}
                                @else
                                    {{ $ekskul->name }}
                                @endif
                            </td>
                            <td>
                                @if($ekskul->student)
                                    <div style="font-weight: 500;">
                                        @if($keyword && stripos($ekskul->student->name, $keyword) !== false)
                                            {!! preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark style="background: rgba(100,108,255,0.3); color: #c7d2fe; border-radius: 3px; padding: 0 2px;">$1</mark>', e($ekskul->student->name)) !!}
                                        @else
                                            {{ $ekskul->student->name }}
                                        @endif
                                    </div>
                                    <small style="color: #64748b; font-size: 11px;">{{ $ekskul->student->nisn }}</small>
                                @else
                                    <span style="color: #f87171; font-size: 12px;"><i class="bi bi-exclamation-circle me-1"></i>Belum ada Admin</span>
                                @endif
                            </td>
                            <td>
                                @if($ekskul->group_link)
                                    <a href="{{ $ekskul->group_link }}" target="_blank"
                                       class="btn btn-sm" style="background: rgba(34,211,238,0.1); color: #22d3ee; font-size: 12px;">
                                        <i class="bi bi-link-45deg"></i> Link
                                    </a>
                                @else
                                    <span style="color: #64748b;">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.ekskul.show', $ekskul->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $ekskul->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.ekskul.destroy', $ekskul->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $ekskul->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $ekskul->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="background: #1e2236; color: #e2e8f0; border: 1px solid #334155;">
                                            <div class="modal-header" style="border-bottom: 1px solid #334155;">
                                                <h5 class="modal-title" id="editModalLabel{{ $ekskul->id }}">Edit Ekskul</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.ekskul.update', $ekskul->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label for="name{{ $ekskul->id }}" class="form-label" style="color: #94a3b8; font-size: 13px;">Nama Ekskul</label>
                                                        <input type="text" class="form-control" style="background: #0f172a; border: 1px solid #334155; color: #e2e8f0;" id="name{{ $ekskul->id }}" name="name" value="{{ old('name', $ekskul->name) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="urutan{{ $ekskul->id }}" class="form-label" style="color: #94a3b8; font-size: 13px;">Urutan</label>
                                                        <input type="number" class="form-control" style="background: #0f172a; border: 1px solid #334155; color: #e2e8f0;" id="urutan{{ $ekskul->id }}" name="urutan" value="{{ old('urutan', $ekskul->urutan) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="student_id{{ $ekskul->id }}" class="form-label" style="color: #94a3b8; font-size: 13px;">Admin Klub (Pilih Siswa)</label>
                                                        <select class="form-select" style="background: #0f172a; border: 1px solid #334155; color: #e2e8f0;" id="student_id{{ $ekskul->id }}" name="student_id">
                                                            <option value="">-- Tidak Ada Admin --</option>
                                                            @foreach($students as $student)
                                                                <option value="{{ $student->id }}" {{ old('student_id', $ekskul->student_id) == $student->id ? 'selected' : '' }}>
                                                                    {{ $student->name }} (NISN: {{ $student->nisn }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small style="color: #64748b; font-size: 11px;">Siswa ini akan menjadi Admin untuk mengelola fitur ekskul ini.</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description{{ $ekskul->id }}" class="form-label" style="color: #94a3b8; font-size: 13px;">Deskripsi</label>
                                                        <textarea class="form-control" style="background: #0f172a; border: 1px solid #334155; color: #e2e8f0;" id="description{{ $ekskul->id }}" name="description" rows="3">{{ old('description', $ekskul->description) }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="group_link{{ $ekskul->id }}" class="form-label" style="color: #94a3b8; font-size: 13px;">Link Grup (WhatsApp/Line)</label>
                                                        <input type="url" class="form-control" style="background: #0f172a; border: 1px solid #334155; color: #e2e8f0;" id="group_link{{ $ekskul->id }}" name="group_link" value="{{ old('group_link', $ekskul->group_link) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="logo{{ $ekskul->id }}" class="form-label" style="color: #94a3b8; font-size: 13px;">Logo Ekskul</label>
                                                        @if($ekskul->logo_url)
                                                            <div class="mb-2">
                                                                <img src="{{ $ekskul->logo_url }}" alt="Logo" width="60" class="rounded" style="border: 2px solid rgba(100,108,255,0.3);">
                                                            </div>
                                                        @endif
                                                        <input class="form-control" style="background: #0f172a; border: 1px solid #334155; color: #e2e8f0;" type="file" id="logo{{ $ekskul->id }}" name="logo" accept="image/*">
                                                        <small style="color: #64748b; font-size: 11px;">Biarkan kosong jika tidak ingin mengubah logo.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" style="border-top: 1px solid #334155;">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5" style="color: #64748b;">
                                <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                                @if(request('search'))
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Tidak ada ekskul yang cocok</div>
                                    <div style="font-size: 13px; margin-top: 4px;">Coba ubah kata kunci pencarian</div>
                                @else
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Belum ada ekskul</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('administrator.components.table-footer', ['data' => $clubs, 'route' => 'admin.ekskul.index'])

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Search clear
    const clearBtn = document.getElementById('clearSearch');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchForm').submit();
        });
    }

    // Sortable JS - only active if not searching
    var el = document.getElementById('sortable-ekskul');
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

                fetch('{{ route('admin.ekskul.reorder') }}', {
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

    // Delete confirm
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Ekskul?',
                text: 'Data ekskul ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#374151',
                background: '#131624',
                color: '#e2e8f0',
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endpush
