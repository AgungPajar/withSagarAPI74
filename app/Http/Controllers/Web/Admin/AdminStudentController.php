<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    public function index()
    {
        $students = Student::with('kelas.jurusan')->orderBy('created_at', 'desc')->paginate(10);
        return view('administrator.siswa.index', compact('students'));
    }

    public function create()
    {
        $kelasList = Kelas::with('jurusan')->orderBy('name', 'asc')->get();
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
        $kelasList = Kelas::with('jurusan')->orderBy('name', 'asc')->get();
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
}
