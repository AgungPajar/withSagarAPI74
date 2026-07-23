@extends('administrator.layouts.app')

@section('title', 'Manage Jurusan')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 style="font-size: 20px; font-weight: 800; color: #e2e8f0; margin: 0;">Manage Jurusan</h1>
        <p style="color: #64748b; font-size: 12px; margin: 4px 0 0;">
            Total {{ $jurusans->total() }} jurusan 
            @if(request('search'))
                <span style="color: #818cf8;"> — hasil pencarian</span>
            @else
                &mdash; drag baris untuk ubah urutan
            @endif
        </p>
    </div>
    <a href="{{ route('admin.jurusan.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Tambah Jurusan
    </a>
</div>

{{-- Search Bar --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.jurusan.index') }}" id="searchForm">
            <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">
                        Cari Nama / Singkatan
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: rgba(30,34,54,0.8); border-color: #1e2236; color: #64748b;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput" class="form-control"
                               placeholder="Nama atau Singkatan Jurusan..." value="{{ request('search') }}" autocomplete="off">
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
                    <a href="{{ route('admin.jurusan.index') }}" class="btn flex-fill"
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
                        <th>Urutan</th>
                        <th>Nama</th>
                        <th>Singkatan</th>
                        <th>Slug</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="{{ request('search') ? '' : 'sortable-jurusans' }}">
                    @forelse($jurusans as $index => $jurusan)
                        @php $keyword = request('search'); @endphp
                        <tr data-id="{{ $jurusan->id }}">
                            <td>
                                @if(!request('search'))
                                    <span class="handle" style="cursor: grab; color: #64748b; font-size: 1.1rem;">⠿</span>
                                @endif
                            </td>
                            <td style="color: #64748b;">{{ $jurusans->firstItem() + $index }}</td>
                            <td><span class="badge" style="background: rgba(100,108,255,0.15); color: #818cf8; border-radius: 6px;">{{ $jurusan->urutan }}</span></td>
                            <td style="font-weight: 600;">
                                @if($keyword && stripos($jurusan->nama, $keyword) !== false)
                                    {!! preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark style="background: rgba(100,108,255,0.3); color: #c7d2fe; border-radius: 3px; padding: 0 2px;">$1</mark>', e($jurusan->nama)) !!}
                                @else
                                    {{ $jurusan->nama }}
                                @endif
                            </td>
                            <td>
                                <code style="font-size: 12px; color: #22d3ee;">
                                    @if($keyword && stripos($jurusan->singkatan, $keyword) !== false)
                                        {!! preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark style="background: rgba(100,108,255,0.3); color: #c7d2fe; border-radius: 3px; padding: 0 2px;">$1</mark>', e($jurusan->singkatan)) !!}
                                    @else
                                        {{ $jurusan->singkatan }}
                                    @endif
                                </code>
                            </td>
                            <td style="color: #64748b; font-size: 12px;">{{ $jurusan->slug }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.jurusan.show', $jurusan->id) }}" class="btn btn-sm" style="background: rgba(34,211,238,0.15); color: #22d3ee;"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.jurusan.edit', $jurusan->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.jurusan.destroy', $jurusan->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5" style="color: #64748b;">
                                <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                                @if(request('search'))
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Tidak ada jurusan yang cocok</div>
                                    <div style="font-size: 13px; margin-top: 4px;">Coba ubah kata kunci pencarian</div>
                                @else
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Belum ada data jurusan</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('administrator.components.table-footer', ['data' => $jurusans, 'route' => 'admin.jurusan.index'])

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

    // Delete confirm
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Jurusan?',
                text: 'Data jurusan ini akan dihapus permanen!',
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
