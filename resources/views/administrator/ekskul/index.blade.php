@extends('administrator.layouts.app')

@section('title', 'Manage Ekskul')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Ekstrakurikuler</h2>
            <a href="{{ route('admin.ekskul.create') }}" class="btn btn-primary">Add New Ekskul</a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Logo</th>
                                <th>Nama Ekskul</th>
                                <th>Admin (Siswa)</th>
                                <th>Group Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clubs as $index => $ekskul)
                                <tr>
                                    <td>{{ $clubs->firstItem() + $index }}</td>
                                    <td>
                                        @if($ekskul->logo_url)
                                            <img src="{{ $ekskul->logo_url }}" alt="Logo" width="50" height="50" class="rounded-circle" style="object-fit: cover;">
                                        @else
                                            <span class="text-muted">No Logo</span>
                                        @endif
                                    </td>
                                    <td>{{ $ekskul->name }}</td>
                                    <td>
                                        @if($ekskul->student)
                                            {{ $ekskul->student->name }} <br>
                                            <small class="text-muted">{{ $ekskul->student->nisn }}</small>
                                        @else
                                            <span class="text-danger">Belum ada Admin</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ekskul->group_link)
                                            <a href="{{ $ekskul->group_link }}" target="_blank">Group Link</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.ekskul.edit', $ekskul->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('admin.ekskul.destroy', $ekskul->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No ekstrakurikuler found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($clubs->hasPages())
                <div class="card-footer">
                    {{ $clubs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
