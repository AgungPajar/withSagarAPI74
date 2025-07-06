<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class ClubRequestController extends Controller
{
    public function index($clubId)
    {
        $requests = DB::table('club_student_requests')
            ->join('students', 'club_student_requests.student_id', '=', 'students.id')
            ->where('club_student_requests.club_id', $clubId)
            ->where('club_student_requests.status', 'pending')
            ->select('club_student_requests.id', 'students.name', 'students.nisn', 'students.class')
            ->get();

        return response()->json($requests);
    }

    public function store($hashId)
    {
        $clubId = Hashids::decode($hashId)[0] ?? null;
        $user = auth()->user();

        if (!$clubId || !$user || !$user->student_id) {
            return response()->json(['message' => 'Data tidak valid'], 400);
        }

        DB::table('club_student_requests')->insert([
            'club_id' => $clubId,
            'student_id' => $user->student_id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Permintaan bergabung telah dikirim!']);
    }

    public function requestJoin($hashId, Request $request)
    {
        $clubId = Hashids::decode($hashId)[0] ?? null;
        if (!$clubId) return response()->json(['message' => 'Invalid club ID'], 404);

        $user = $request->user();
        if ($user->role !== 'student') return response()->json(['message' => 'Unauthorized'], 403);

        $student = Student::where('user_id', $user->id)->first();
        if (!$student) return response()->json(['message' => 'Student not found'], 404);

        DB::table('club_student_requests')->updateOrInsert(
            ['club_id' => $clubId, 'student_id' => $student->id],
            ['status' => 'pending', 'updated_at' => now()]
        );

        return response()->json(['message' => 'Permintaan bergabung telah dikirim. Menunggu konfirmasi.']);
    }



    public function pendingRequests($hashedId)
    {
        $clubId = Hashids::decode($hashedId)[0] ?? null;

        if (!$clubId) {
            return response()->json(['message' => 'Club not found'], 404);
        }

        $requests = DB::table('club_student_requests')
            ->join('students', 'club_student_requests.student_id', '=', 'students.id')
            ->where('club_student_requests.club_id', $clubId)
            ->where('club_student_requests.status', 'pending')
            ->select('club_student_requests.id', 'students.name', 'students.class', 'students.nisn')
            ->get();

        return response()->json($requests);
    }

    public function confirmRequest($hashedClubId, $requestId, Request $request)
    {
        $clubId = Hashids::decode($hashedClubId)[0] ?? null;
        if (!$clubId) {
            return response()->json(['message' => 'Club tidak valid'], 400);
        }

        $status = $request->input('status');
        $validStatus = ['accepted', 'rejected'];

        if (!in_array($status, $validStatus)) {
            return response()->json(['message' => 'Status tidak valid'], 400);
        }

        // Ambil data request
        $requestData = DB::table('club_student_requests')
            ->where('id', $requestId)
            ->where('club_id', $clubId)
            ->first();

        if (!$requestData) {
            return response()->json(['message' => 'Request tidak ditemukan'], 404);
        }

        // Update status request
        DB::table('club_student_requests')
            ->where('id', $requestId)
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);

        // Kalau diterima, cek dulu biar gak dobel insert
        if ($status === 'accepted') {
            $alreadyJoined = DB::table('club_student')
                ->where('club_id', $clubId)
                ->where('student_id', $requestData->student_id)
                ->exists();

            if (!$alreadyJoined) {
                DB::table('club_student')->insert([
                    'club_id' => $clubId,
                    'student_id' => $requestData->student_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json(['message' => 'Status updated']);
    }


    public function getAcceptedMembers($hashedId)
    {
        $decoded = Hashids::decode($hashedId);

        if (count($decoded) === 0) {
            return response()->json([
                'message' => 'Ekskul tidak ditemukan (hash_id tidak valid)'
            ], 404);
        }

        $clubId = $decoded[0];

        $members = DB::table('club_student')
            ->join('students', 'club_student.student_id', '=', 'students.id')
            ->leftJoin('jurusans', 'students.id_jurusan', '=', 'jurusans.id')

            ->where('club_student.club_id', $clubId)
            ->select(
                'club_student.id as club_student_id', // Kalau butuh ID buat hapus dari relasi
                'students.id',
                'students.nisn',
                'students.name',
                'students.class',
                'students.phone',
                'students.rombel',
                'jurusans.singkatan as jurusan_singkatan' 
            )
            ->get();

        return response()->json($members);
    }


    public function deleteMember($hashedClubId, $studentId)
    {
        $decoded = Hashids::decode($hashedClubId);
        if (count($decoded) === 0) {
            return response()->json(['message' => 'Club tidak valid'], 400);
        }

        $clubId = $decoded[0];

        // Hapus dari tabel relasi jika ada
        DB::table('club_student')
            ->where('club_id', $clubId)
            ->where('student_id', $studentId)
            ->delete();

        // Tetap update status menjadi rejected (fallback)
        $updated = DB::table('club_student_requests')
            ->where('club_id', $clubId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['accepted', 'pending'])
            ->update([
                'status' => 'rejected',
                'updated_at' => now()
            ]);
            


        if ($updated) {
            return response()->json(['message' => 'Anggota berhasil dihapus & status diubah ke rejected']);
        } else {
            return response()->json(['message' => 'Status tidak bisa diubah karena data tidak ditemukan'], 404);
        }
    }
}
