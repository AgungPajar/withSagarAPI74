<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\RegistrasiAgustusan;
use Illuminate\Support\Facades\Validator;

class AgustusanController extends Controller
{
    public function searchByNisn(Request $request)
    {
        $nisn = $request->query('nisn');

        if (!$nisn) {
            return response()->json(['error' => 'NISN harus diisi'], 400);
        }

        $student = Student::with('jurusan')
            ->where('nisn', $nisn)
            ->first();

        if (!$student) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 400);
        }

        return response()->json([
            'id' => $student->id,
            'nama' => $student->name,
            'kelas' => $student->kelas_lengkap,
            'nisn' => $student->nisn,
            'jurusan' => $student->jurusan ? [
                'id' => $student->jurusan->id,
                'nama' => $student->jurusan->nama,
                'singkatan' => $student->jurusan->singkatan,
            ] : null,
        ]);
    }

    public function registerAgustus(Request $request)
    {
        $request->validate([
            'nomor_hp' => 'required|string',
            'nama_tim' => 'nullable|string',
            'cabang_lomba' => 'required|string',
            'anggota' => 'required|array|min:3',
            'anggota.*.student_id' => 'required|exists:students,id',
            'anggota.*.id_jurusan' => 'nullable|exists:jurusans,id',
        ]);

        // Simpan data pendaftaran utama
        $registration = RegistrasiAgustusan::create([
            'nomor_hp' => $request->nomor_hp,
            'nama_tim' => $request->nama_tim,
            'cabang_lomba' => $request->cabang_lomba,
        ]);

        $idJurusanTim = $request->input('id_jurusan');

        // Simpan anggota
        foreach ($request->anggota as $anggota) {
            $registration->members()->create([
                'student_id' => $anggota['student_id'],
                'id_jurusan' => $idJurusanTim,
            ]);
        }

        return response()->json(['message' => 'Pendaftaran berhasil!'], 201);
    }

    public function getJurusans()
    {
        $jurusans = Jurusan::select('id', 'nama')->orderBy('nama')->get();
        return response()->json($jurusans);
    }

    public function listPendaftar()
    {
        // Ambil semua pendaftaran beserta anggota dan jurusannya
        $pendaftar = RegistrasiAgustusan::with('members.student', 'members.jurusan')->get();

        return response()->json($pendaftar);
    }

}
