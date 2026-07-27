@extends('administrator.layouts.app')

@section('title', 'Detail Ekskul')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 style="font-size: 20px; font-weight: 800; color: #e2e8f0; margin: 0;">Detail Ekskul: {{ $ekskul->name }}</h1>
        <p style="color: #64748b; font-size: 12px; margin: 4px 0 0;">
            Kelola detail, pendaftar, dan anggota
        </p>
    </div>
    <a href="{{ route('admin.ekskul.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-2" style="background: rgba(100,116,139,0.2); border: 1px solid rgba(100,116,139,0.3); color: #cbd5e1;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header" style="background: rgba(30,34,54,0.5); border-bottom: 1px solid #1e2236;">
                <h5 class="mb-0" style="font-size: 15px; font-weight: 700; color: #e2e8f0;">Informasi Ekskul</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($ekskul->logo_url)
                        <img src="{{ $ekskul->logo_url }}" alt="Logo" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid rgba(100,108,255,0.3);">
                    @else
                        <div class="rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: rgba(100,108,255,0.15); color: #818cf8; font-size: 32px; font-weight: 700;">
                            {{ strtoupper(substr($ekskul->name, 0, 1)) }}
                        </div>
                    @endif
                    <h5 style="color: #e2e8f0; font-weight: 700;">{{ $ekskul->name }}</h5>
                    <p style="color: #94a3b8; font-size: 13px;">{{ $ekskul->description ?: 'Tidak ada deskripsi' }}</p>
                </div>
                
                <hr style="border-color: #334155;">
                
                <div class="mb-3">
                    <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">Admin Ekskul (Siswa)</label>
                    <div style="color: #e2e8f0; font-size: 14px; font-weight: 500;">
                        @if($ekskul->student)
                            {{ $ekskul->student->name }} <br>
                            <small style="color: #94a3b8; font-size: 12px;">NISN: {{ $ekskul->student->nisn }}</small>
                        @else
                            <span style="color: #f87171; font-size: 13px;"><i class="bi bi-exclamation-circle me-1"></i>Belum ada Admin</span>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b;">Link Grup</label>
                    <div style="font-size: 14px;">
                        @if($ekskul->group_link)
                            <a href="{{ $ekskul->group_link }}" target="_blank" style="color: #22d3ee; text-decoration: none;">
                                <i class="bi bi-link-45deg"></i> Buka Link
                            </a>
                        @else
                            <span style="color: #64748b;">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        {{-- Tabs --}}
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-anggota-tab" data-bs-toggle="pill" data-bs-target="#pills-anggota" type="button" role="tab" aria-controls="pills-anggota" aria-selected="true" style="border-radius: 8px;">
                    Anggota ({{ $ekskul->students->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-pendaftar-tab" data-bs-toggle="pill" data-bs-target="#pills-pendaftar" type="button" role="tab" aria-controls="pills-pendaftar" aria-selected="false" style="border-radius: 8px;">
                    Pendaftar Pending ({{ $pendaftar->count() }})
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="pills-tabContent">
            {{-- Tab Anggota --}}
            <div class="tab-pane fade show active" id="pills-anggota" role="tabpanel" aria-labelledby="pills-anggota-tab">
                <div class="card">
                    <div class="card-body p-0">
                        <form action="{{ route('admin.ekskul.bulk-delete-members', $ekskul->id) }}" method="POST" id="form-anggota">
                            @csrf
                            <div class="d-flex p-3 align-items-center" style="background: rgba(30,34,54,0.3); border-bottom: 1px solid #1e2236; gap: 10px;">
                                <select name="action" class="form-select form-select-sm w-auto" style="background: #0f172a; border-color: #334155; color: #e2e8f0;" required>
                                    <option value="">Pilih Aksi...</option>
                                    <option value="delete">Hapus Terpilih</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary" onclick="confirmSubmit('form-anggota')">Terapkan</button>
                            </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input class="form-check-input" type="checkbox" id="selectAllAnggota">
                                        </th>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ekskul->students as $index => $anggota)
                                        <tr>
                                            <td>
                                                <input class="form-check-input check-anggota" type="checkbox" name="student_ids[]" value="{{ $anggota->id }}">
                                            </td>
                                            <td style="color: #64748b;">{{ $index + 1 }}</td>
                                            <td style="font-weight: 500; color: #e2e8f0;">{{ $anggota->name }}</td>
                                            <td style="color: #94a3b8;">{{ $anggota->nisn }}</td>
                                            <td style="color: #94a3b8;">{{ $anggota->kelas ? $anggota->kelas->nama : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5" style="color: #64748b;">
                                                <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                                                <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Belum ada anggota</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            
            {{-- Tab Pendaftar --}}
            <div class="tab-pane fade" id="pills-pendaftar" role="tabpanel" aria-labelledby="pills-pendaftar-tab">
                <div class="card">
                    <div class="card-body p-0">
                        <form action="{{ route('admin.ekskul.bulk-delete-requests', $ekskul->id) }}" method="POST" id="form-pendaftar">
                            @csrf
                            <div class="d-flex p-3 align-items-center" style="background: rgba(30,34,54,0.3); border-bottom: 1px solid #1e2236; gap: 10px;">
                                <select name="action" class="form-select form-select-sm w-auto" style="background: #0f172a; border-color: #334155; color: #e2e8f0;" required>
                                    <option value="">Pilih Aksi...</option>
                                    <option value="delete">Hapus Terpilih</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary" onclick="confirmSubmit('form-pendaftar')">Terapkan</button>
                            </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input class="form-check-input" type="checkbox" id="selectAllPendaftar">
                                        </th>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Kelas</th>
                                        <th>Tanggal Daftar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendaftar as $index => $p)
                                        <tr>
                                            <td>
                                                <input class="form-check-input check-pendaftar" type="checkbox" name="student_ids[]" value="{{ $p->id }}">
                                            </td>
                                            <td style="color: #64748b;">{{ $index + 1 }}</td>
                                            <td style="font-weight: 500; color: #e2e8f0;">{{ $p->name }}</td>
                                            <td style="color: #94a3b8;">{{ $p->nisn }}</td>
                                            <td style="color: #94a3b8;">{{ $p->kelas_nama ?: '-' }}</td>
                                            <td style="color: #94a3b8;">{{ \Carbon\Carbon::parse($p->request_date)->format('d M Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5" style="color: #64748b;">
                                                <i class="bi bi-person-lines-fill fs-1 d-block mb-3 opacity-25"></i>
                                                <div style="font-size: 15px; font-weight: 600; color: #94a3b8;">Tidak ada pendaftar pending</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAllAnggota = document.getElementById('selectAllAnggota');
    if (checkAllAnggota) {
        checkAllAnggota.addEventListener('change', function() {
            document.querySelectorAll('.check-anggota').forEach(cb => cb.checked = this.checked);
        });
    }

    const checkAllPendaftar = document.getElementById('selectAllPendaftar');
    if (checkAllPendaftar) {
        checkAllPendaftar.addEventListener('change', function() {
            document.querySelectorAll('.check-pendaftar').forEach(cb => cb.checked = this.checked);
        });
    }
});

function confirmSubmit(formId) {
    const form = document.getElementById(formId);
    const action = form.querySelector('select[name="action"]').value;
    
    if (!action) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Aksi',
            text: 'Silakan pilih aksi terlebih dahulu!',
            background: '#131624',
            color: '#e2e8f0',
        });
        return;
    }
    
    const checkboxes = form.querySelectorAll('input[type="checkbox"]:not([id^="selectAll"])');
    let hasChecked = false;
    checkboxes.forEach(cb => {
        if (cb.checked) hasChecked = true;
    });

    if (!hasChecked) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Data',
            text: 'Silakan pilih minimal satu data!',
            background: '#131624',
            color: '#e2e8f0',
        });
        return;
    }

    Swal.fire({
        title: 'Apakah kamu yakin?',
        text: 'Data yang dipilih akan dihapus permanen dari ekskul ini!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#374151',
        background: '#131624',
        color: '#e2e8f0',
    }).then(result => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>
@endpush
