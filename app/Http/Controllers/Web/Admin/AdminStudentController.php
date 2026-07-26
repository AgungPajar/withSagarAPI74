<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage    = in_array((int) $request->perPage, [10, 25, 50]) ? (int) $request->perPage : 10;
        $search     = $request->search;
        $kelasFilter   = $request->kelas_id;
        $jurusanFilter = $request->jurusan_id;

        $students = Student::with('kelas.jurusan')
            ->select('students.*')
            ->leftJoin('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->leftJoin('jurusans', 'kelas.jurusan_id', '=', 'jurusans.id')
            ->orderBy('jurusans.urutan', 'asc')
            ->orderBy('kelas.nama', 'asc')
            ->orderBy('students.name', 'asc')
            ->when($search, function($q) use ($search) {
                $q->where(function($q2) use ($search) {
                    $q2->where('students.name', 'ilike', "%{$search}%")
                       ->orWhere('students.nisn', 'ilike', "%{$search}%")
                       ->orWhereHas('user', function($q3) use ($search) {
                           $q3->where('username', 'ilike', "%{$search}%");
                       });
                });
            })
            ->when($kelasFilter, fn($q) => $q->where('kelas_id', $kelasFilter))
            ->when($jurusanFilter, function($q) use ($jurusanFilter) {
                $q->whereHas('kelas', fn($q2) => $q2->where('jurusan_id', $jurusanFilter));
            })
            ->paginate($perPage)
            ->withQueryString();

        $kelasList  = Kelas::with('jurusan')->orderBy('nama')->get();
        $jurusans   = \App\Models\Jurusan::orderBy('urutan')->get();

        return view('administrator.siswa.index', compact('students', 'kelasList', 'jurusans'));
    }

    public function create()
    {
        $kelasList = Kelas::with('jurusan')->orderBy('nama', 'asc')->get();
        return view('administrator.siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|max:255|unique:students,nisn',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $user = User::firstOrCreate(
            ['username' => $request->nisn],
            [
                'name' => $request->name,
                'role' => 'student',
                'password' => bcrypt('ossagar123'),
            ]
        );

        Student::create([
            'name' => $request->name,
            'nisn' => $request->nisn,
            'kelas_id' => $request->kelas_id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa created successfully.');
    }

    public function edit(Student $siswa)
    {
        $kelasList = Kelas::with('jurusan')->orderBy('nama', 'asc')->get();
        return view('administrator.siswa.edit', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, Student $siswa)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|max:255|unique:students,nisn,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $data = $request->only('name', 'nisn', 'kelas_id');

        // Update or create user account linked to this student
        $user = User::where('username', $request->nisn)->first();
        if (!$user) {
            $user = User::create([
                'username' => $request->nisn,
                'name' => $request->name,
                'role' => 'student',
                'password' => bcrypt('ossagar123'),
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'role' => 'student',
            ]);
        }

        $data['user_id'] = $user->id;

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa updated successfully.');
    }

    public function destroy(Student $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa deleted successfully.');
    }

    public function resetPassword(Student $siswa)
    {
        if ($siswa->user_id) {
            $user = User::find($siswa->user_id);
            if ($user) {
                $user->update([
                    'password' => bcrypt('ossagar123')
                ]);
                return redirect()->back()->with('success', 'Password akun ' . $siswa->name . ' berhasil direset menjadi: ossagar123');
            }
        }
        return redirect()->back()->with('error', 'Akun user tidak ditemukan untuk siswa ini.');
    }
}
