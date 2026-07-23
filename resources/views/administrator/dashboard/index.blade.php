@extends('administrator.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-6" style="margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: #e2e8f0; margin: 0;">Dashboard</h1>
        <p style="color: #64748b; font-size: 13px; margin: 4px 0 0;">Selamat datang, {{ auth('admin')->user()->name }}!</p>
    </div>
    <div style="color: #64748b; font-size: 12px; background: rgba(100,108,255,0.1); padding: 6px 12px; border-radius: 20px; border: 1px solid rgba(100,108,255,0.2);">
        <i class="bi bi-calendar3 me-2"></i>{{ now()->translatedFormat('l, d F Y') }}
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card accent-blue">
            <div class="stat-icon" style="background: rgba(100,108,255,0.15); color: #818cf8;">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="stat-value" style="color: #818cf8;">{{ $stats['jurusan'] ?? 0 }}</div>
            <div class="stat-label">Total Jurusan</div>
            <a href="{{ route('admin.jurusan.index') }}" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card accent-green">
            <div class="stat-icon" style="background: rgba(34,211,238,0.12); color: #22d3ee;">
                <i class="bi bi-door-open"></i>
            </div>
            <div class="stat-value" style="color: #22d3ee;">{{ $stats['kelas'] ?? 0 }}</div>
            <div class="stat-label">Total Kelas</div>
            <a href="{{ route('admin.kelas.index') }}" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card accent-purple">
            <div class="stat-icon" style="background: rgba(167,139,250,0.12); color: #a78bfa;">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value" style="color: #a78bfa;">{{ $stats['siswa'] ?? 0 }}</div>
            <div class="stat-label">Total Siswa</div>
            <a href="{{ route('admin.siswa.index') }}" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card accent-orange">
            <div class="stat-icon" style="background: rgba(245,158,11,0.12); color: #fbbf24;">
                <i class="bi bi-trophy"></i>
            </div>
            <div class="stat-value" style="color: #fbbf24;">{{ $stats['ekskul'] ?? 0 }}</div>
            <div class="stat-label">Total Ekskul</div>
            <a href="{{ route('admin.ekskul.index') }}" class="stretched-link"></a>
        </div>
    </div>
</div>

<!-- Quick Access -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge" style="color: #646cff;"></i>
                Akses Cepat
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                    $quickLinks = [
                        ['route' => 'admin.jurusan.index', 'icon' => 'bi-diagram-3', 'label' => 'Manage Jurusan', 'color' => '#818cf8', 'bg' => 'rgba(100,108,255,0.1)'],
                        ['route' => 'admin.kelas.index', 'icon' => 'bi-door-open', 'label' => 'Manage Kelas', 'color' => '#22d3ee', 'bg' => 'rgba(34,211,238,0.1)'],
                        ['route' => 'admin.siswa.index', 'icon' => 'bi-people', 'label' => 'Manage Siswa', 'color' => '#a78bfa', 'bg' => 'rgba(167,139,250,0.1)'],
                        ['route' => 'admin.ekskul.index', 'icon' => 'bi-trophy', 'label' => 'Manage Ekskul', 'color' => '#fbbf24', 'bg' => 'rgba(245,158,11,0.1)'],
                    ];
                    @endphp
                    @foreach($quickLinks as $link)
                    <div class="col-sm-6">
                        <a href="{{ route($link['route']) }}"
                           class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                           style="background: {{ $link['bg'] }}; border: 1px solid {{ $link['bg'] }}; transition: all 0.2s;"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.3)'"
                           onmouseout="this.style.transform=''; this.style.boxShadow=''">
                            <div style="width:42px;height:42px;background:rgba(0,0,0,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi {{ $link['icon'] }}" style="font-size:18px;color:{{ $link['color'] }};"></i>
                            </div>
                            <span style="color: #e2e8f0; font-size: 13.5px; font-weight: 600;">{{ $link['label'] }}</span>
                            <i class="bi bi-chevron-right ms-auto" style="color: {{ $link['color'] }}; font-size: 12px;"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-info-circle" style="color: #22d3ee;"></i>
                Info Sistem
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0" style="font-size:13px;">
                    @php
                    $infos = [
                        ['label' => 'Laravel Version', 'value' => app()->version(), 'icon' => 'bi-code-slash'],
                        ['label' => 'PHP Version', 'value' => PHP_VERSION, 'icon' => 'bi-cpu'],
                        ['label' => 'Timezone', 'value' => config('app.timezone'), 'icon' => 'bi-clock'],
                        ['label' => 'Environment', 'value' => app()->environment(), 'icon' => 'bi-server'],
                    ];
                    @endphp
                    @foreach($infos as $info)
                    <li class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px solid rgba(30,34,54,0.8);">
                        <span style="color: #64748b;">
                            <i class="bi {{ $info['icon'] }} me-2"></i>{{ $info['label'] }}
                        </span>
                        <span class="badge" style="background: rgba(100,108,255,0.15); color: #818cf8; font-size: 11px; font-weight: 600; border-radius: 6px;">
                            {{ $info['value'] }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
