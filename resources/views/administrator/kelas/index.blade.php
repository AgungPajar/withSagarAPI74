@extends('administrator.layouts.app')

@section('title', 'Manage Kelas')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 style="font-size: 20px; font-weight: 800; color: #e2e8f0; margin: 0;">Manage Kelas</h1>
        <p style="color: #64748b; font-size: 12px; margin: 4px 0 0;">
            {{ $kelas->total() }} kelas ditemukan
            @if(request('search') || request('jurusan_id'))
                <span style="color: #818cf8;"> — hasil filter</span>
            @endif
        </p>
    </div>
    <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Tambah Kelas
    </a>
</div>

{{-- Search & Filter Bar --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.kelas.index') }}" id="searchForm">
            <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
            <div class="row g-3 align-items-end">
                {{-- Search nama kelas --}}
                <div class="col-12 col-md-5">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">
                        Cari Nama Kelas
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: rgba(30,34,54,0.8); border-color: #1e2236; color: #64748b;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput" class="form-control"
                               placeholder="Contoh: XII DKV 1, X IPS..." value="{{ request('search') }}"
                               autocomplete="off">
                        @if(request('search'))
                        <button type="button" class="btn btn-sm" id="clearSearch"
                                style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; border-left: none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Filter jurusan --}}
                <div class="col-12 col-md-4">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">
                        Filter Jurusan
                    </label>
                    <select name="jurusan_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama }} ({{ $j->singkatan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if(request('search') || request('jurusan_id'))
                    <a href="{{ route('admin.kelas.index') }}" class="btn flex-fill"
                       style="background: rgba(100,108,255,0.1); color: #818cf8; border: 1px solid rgba(100,108,255,0.2);">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Slug</th>
                        <th>Jurusan</th>
                        <th>Total Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $index => $k)
                        <tr>
                            <td style="color: #64748b;">{{ $kelas->firstItem() + $index }}</td>
                            <td style="font-weight: 600;">
                                @if(request('search') && str_contains(strtolower($k->nama), strtolower(request('search'))))
                                    {!! preg_replace(
                                        '/(' . preg_quote(request('search'), '/') . ')/i',
                                        '<mark style="background: rgba(100,108,255,0.3); color: #c7d2fe; border-radius: 3px; padding: 0 2px;">$1</mark>',
                                        e($k->nama)
                                    ) !!}
                                @else
                                    {{ $k->nama }}
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 12px;">{{ $k->slug }}</td>
                            <td>
                                @if($k->jurusan)
                                    <span class="badge" style="background: rgba(34,211,238,0.12); color: #22d3ee; border-radius: 6px; font-size: 12px;">
                                        {{ $k->jurusan->singkatan }}
                                    </span>
                                    <span style="font-size: 13px; color: #94a3b8; margin-left: 4px;">{{ $k->jurusan->nama }}</span>
                                @else
                                    <span style="color: #64748b;">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(167,139,250,0.15); color: #a78bfa; border-radius: 6px; font-size: 12px;">
                                    <i class="bi bi-people-fill me-1"></i>{{ $k->students_count }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.kelas.show', $k->id) }}"
                                       class="btn btn-sm" style="background: rgba(34,211,238,0.12); color: #22d3ee;" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-id="{{ $k->id }}"
                                        data-tingkatan="{{ $k->tingkatan }}"
                                        data-jurusan="{{ $k->jurusan_id }}"
                                        data-rombel="{{ $k->rombel }}"
                                        data-nama="{{ $k->nama }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color: #64748b;">
                                <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                                @if(request('search') || request('jurusan_id'))
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Tidak ada kelas yang cocok</div>
                                    <div style="font-size: 13px; margin-top: 4px;">Coba ubah kata kunci atau filter yang kamu gunakan</div>
                                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-sm mt-3"
                                       style="background: rgba(100,108,255,0.15); color: #818cf8; border: 1px solid rgba(100,108,255,0.25);">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                    </a>
                                @else
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Belum ada kelas</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('administrator.components.table-footer', ['data' => $kelas, 'route' => 'admin.kelas.index'])

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="bi bi-pencil-square me-2" style="color: #646cff;"></i>Edit Kelas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tingkatan" class="form-label">Tingkatan</label>
                        <select class="form-select" id="edit_tingkatan" name="tingkatan" required>
                            <option value="">Pilih Tingkatan...</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_rombel" class="form-label">Rombel <small style="color:#64748b;">(Contoh: 1, 2, A, B)</small></label>
                        <input type="text" class="form-control" id="edit_rombel" name="rombel" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jurusan_id" class="form-label">Jurusan</label>
                        <select class="form-select" id="edit_jurusan_id" name="jurusan_id" required>
                            <option value="">Pilih Jurusan...</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" data-singkatan="{{ $jurusan->singkatan }}">
                                    {{ $jurusan->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Kelas <small style="color:#64748b;">(otomatis terisi)</small></label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== Delete with SweetAlert =====
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Kelas?',
                text: 'Data kelas ini akan dihapus permanen!',
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

    // ===== Clear search button =====
    const clearBtn = document.getElementById('clearSearch');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchForm').submit();
        });
    }

    // ===== Edit modal =====
    const editButtons = document.querySelectorAll('.btn-edit');
    const editForm = document.getElementById('editForm');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id         = this.getAttribute('data-id');
            const tingkatan  = this.getAttribute('data-tingkatan');
            const jurusan_id = this.getAttribute('data-jurusan');
            const rombel     = this.getAttribute('data-rombel');
            const nama       = this.getAttribute('data-nama');

            editForm.action = `/admin/kelas/${id}`;
            document.getElementById('edit_tingkatan').value  = tingkatan  || '';
            document.getElementById('edit_jurusan_id').value = jurusan_id || '';
            document.getElementById('edit_rombel').value     = rombel     || '';
            document.getElementById('edit_nama').value       = nama       || '';
        });
    });

    // ===== Auto-generate nama kelas =====
    function updateNamaKelas() {
        const tingkatan     = document.getElementById('edit_tingkatan').value;
        const jurusanSelect = document.getElementById('edit_jurusan_id');
        const singkatan     = jurusanSelect.options[jurusanSelect.selectedIndex]?.getAttribute('data-singkatan') || '';
        const rombel        = document.getElementById('edit_rombel').value;

        if (tingkatan && singkatan && rombel) {
            document.getElementById('edit_nama').value = `${tingkatan} ${singkatan} ${rombel}`;
        }
    }

    document.getElementById('edit_tingkatan').addEventListener('change', updateNamaKelas);
    document.getElementById('edit_jurusan_id').addEventListener('change', updateNamaKelas);
    document.getElementById('edit_rombel').addEventListener('input', updateNamaKelas);
});
</script>
@endpush
