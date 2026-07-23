@extends('administrator.layouts.app')

@section('title', 'Manage Siswa')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Siswa</h2>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">Create New Siswa</a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $s)
                            <tr>
                                <td>{{ $students->firstItem() + $index }}</td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->nisn }}</td>
                                <td>{{ $s->kelas ? $s->kelas->name : 'N/A' }}</td>
                                <td>{{ ($s->kelas && $s->kelas->jurusan) ? $s->kelas->jurusan->name : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.siswa.edit', $s->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.siswa.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Siswa found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
