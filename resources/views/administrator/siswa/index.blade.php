@extends('administrator.layouts.app')

@section('title', 'Manage Siswa')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 style="font-size: 20px; font-weight: 800; color: #e2e8f0; margin: 0;">Manage Siswa</h1>
        <p style="color: #64748b; font-size: 12px; margin: 4px 0 0;">
            {{ $students->total() }} siswa ditemukan
            @if(request('search') || request('kelas_id') || request('jurusan_id'))
                <span style="color: #818cf8;"> — hasil filter</span>
            @endif
        </p>
    </div>
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Tambah Siswa
    </a>
</div>

{{-- Search & Filter Bar --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.siswa.index') }}" id="searchForm">
            <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
            <div class="row g-3 align-items-end">

                {{-- Search nama / NISN --}}
                <div class="col-12 col-md-4">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">
                        Cari Nama / NISN
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: rgba(30,34,54,0.8); border-color: #1e2236; color: #64748b;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput" class="form-control"
                               placeholder="Nama atau NISN..." value="{{ request('search') }}" autocomplete="off">
                        @if(request('search'))
                        <button type="button" id="clearSearch"
                                class="btn btn-sm" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; border-left: none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Filter Jurusan --}}
                <div class="col-12 col-md-3">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">
                        Filter Jurusan
                    </label>
                    <select name="jurusan_id" class="form-select" onchange="this.form.submit(); document.querySelector('[name=kelas_id]').value='';">
                        <option value="">-- Semua Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama }} ({{ $j->singkatan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kelas --}}
                <div class="col-12 col-md-3">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">
                        Filter Kelas
                    </label>
                    <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $kls)
                            @if(!request('jurusan_id') || $kls->jurusan_id == request('jurusan_id'))
                                <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if(request('search') || request('kelas_id') || request('jurusan_id'))
                    <a href="{{ route('admin.siswa.index') }}" class="btn flex-fill"
                       style="background: rgba(100,108,255,0.1); color: #818cf8; border: 1px solid rgba(100,108,255,0.2);" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
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
                        <th>Nama</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $s)
                        @php $keyword = request('search'); @endphp
                        <tr>
                            <td style="color: #64748b;">{{ $students->firstItem() + $index }}</td>
                            <td style="font-weight: 500;">
                                @if($keyword && stripos($s->name, $keyword) !== false)
                                    {!! preg_replace(
                                        '/(' . preg_quote($keyword, '/') . ')/i',
                                        '<mark style="background: rgba(100,108,255,0.3); color: #c7d2fe; border-radius: 3px; padding: 0 2px;">$1</mark>',
                                        e($s->name)
                                    ) !!}
                                @else
                                    {{ $s->name }}
                                @endif
                            </td>
                            <td>
                                <code style="font-size: 12px; color: #818cf8;">
                                    @if($keyword && stripos($s->nisn, $keyword) !== false)
                                        {!! preg_replace(
                                            '/(' . preg_quote($keyword, '/') . ')/i',
                                            '<mark style="background: rgba(100,108,255,0.3); color: #c7d2fe; border-radius: 3px; padding: 0 2px;">$1</mark>',
                                            e($s->nisn)
                                        ) !!}
                                    @else
                                        {{ $s->nisn }}
                                    @endif
                                </code>
                            </td>
                            <td>
                                @if($s->kelas)
                                    <span class="badge" style="background: rgba(34,211,238,0.12); color: #22d3ee; border-radius: 6px; font-size: 12px;">
                                        {{ $s->kelas->nama }}
                                    </span>
                                @else
                                    <span style="color: #64748b; font-size: 12px;">-</span>
                                @endif
                            </td>
                            <td style="color: #94a3b8; font-size: 13px;">
                                {{ ($s->kelas && $s->kelas->jurusan) ? $s->kelas->jurusan->nama : '-' }}
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.siswa.edit', $s->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.siswa.destroy', $s->id) }}" method="POST" class="d-inline delete-form">
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
                                @if(request('search') || request('kelas_id') || request('jurusan_id'))
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Tidak ada siswa yang cocok</div>
                                    <div style="font-size: 13px; margin-top: 4px;">Coba ubah kata kunci atau filter yang kamu gunakan</div>
                                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm mt-3"
                                       style="background: rgba(100,108,255,0.15); color: #818cf8; border: 1px solid rgba(100,108,255,0.25);">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                    </a>
                                @else
                                    <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Belum ada data siswa</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('administrator.components.table-footer', ['data' => $students, 'route' => 'admin.siswa.index'])

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Delete confirm
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Siswa?',
                text: 'Data siswa ini akan dihapus permanen!',
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

    // Clear search
    const clearBtn = document.getElementById('clearSearch');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchForm').submit();
        });
    }
});
</script>
@endpush
