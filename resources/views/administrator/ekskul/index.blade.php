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
                        <th>#</th>
                        <th>Logo</th>
                        <th>Nama Ekskul</th>
                        <th>Admin (Siswa)</th>
                        <th>Group Link</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clubs as $index => $ekskul)
                        @php $keyword = request('search'); @endphp
                        <tr>
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
                                    <a href="{{ route('admin.ekskul.edit', $ekskul->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.ekskul.destroy', $ekskul->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color: #64748b;">
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
