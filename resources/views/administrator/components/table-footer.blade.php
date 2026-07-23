{{-- 
    Partial: components/table-footer.blade.php
    Usage: @include('administrator.components.table-footer', ['data' => $students, 'route' => 'admin.siswa.index'])
    $data  = the paginator instance
    $route = named route for the page
--}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4">

    {{-- Show per page selector --}}
    <div class="d-flex align-items-center gap-2" style="font-size: 13px; color: var(--text-muted, #64748b);">
        <span>Tampilkan</span>
        <form method="GET" action="{{ route($route) }}" id="perPageForm_{{ Str::random(4) }}" class="d-inline">
            <select name="perPage" class="form-select form-select-sm d-inline-block"
                    style="width: auto; font-size: 13px; background: rgba(30,34,54,0.9); border-color: #1e2236; color: #e2e8f0;"
                    onchange="this.closest('form').submit()">
                @foreach([10, 25, 50] as $opt)
                    <option value="{{ $opt }}" {{ (int) request('perPage', 10) === $opt ? 'selected' : '' }}>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        </form>
        <span>data per halaman &mdash;
            Menampilkan <strong style="color: #e2e8f0;">{{ $data->firstItem() ?? 0 }}</strong>
            &ndash;
            <strong style="color: #e2e8f0;">{{ $data->lastItem() ?? 0 }}</strong>
            dari <strong style="color: #e2e8f0;">{{ $data->total() }}</strong> total
        </span>
    </div>

    {{-- Pagination links --}}
    <div>
        {{ $data->links('vendor.pagination.bootstrap-5') }}
    </div>

</div>
