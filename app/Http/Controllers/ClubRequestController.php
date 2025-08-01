<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class ClubRequestController extends Controller
{
    private function resolveClubId($input)
    {
        if (is_numeric($input)) return intval($input); // tangkap angka dulu bro
        $decoded = Hashids::decode($input);
        return count($decoded) > 0 ? $decoded[0] : null;
    }

    // private function authorizeClub($clubId)
    // {
    //     $club = Club::find($clubId);
    //     if (!$club || $club->user_id !== auth()->id()) {
    //         abort(403, 'Unauthorized access to this club.');
    //     }
    //     return $club;
    // }

    public function index($clubId)
    {
        $clubId = $this->resolveClubId($clubId);
        // $this->authorizeClub($clubId);

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
        $clubId = $this->resolveClubId($hashId);
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
        $clubId = $this->resolveClubId($hashId);
        // $this->authorizeClub($clubId);
        
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
        $clubId = $this->resolveClubId($hashedId);
        // $this->authorizeClub($clubId);
        
        if (!$clubId) return response()->json(['message' => 'Club not found'], 404);

        $requests = DB::table('club_student_requests')
            ->join('students', 'club_student_requests.student_id', '=', 'students.id')
            ->leftJoin('jurusans', 'students.id_jurusan', '=', 'jurusans.id')
            ->where('club_student_requests.club_id', $clubId)
            ->where('club_student_requests.status', 'pending')
            ->select(
                'club_student_requests.id', 
                'students.name', 
                'students.class',
                'students.rombel',
                'jurusans.singkatan as jurusan_singkatan',
                'students.nisn',
            )
            ->get();

        return response()->json($requests);
    }

    public function confirmRequest($hashedClubId, $requestId, Request $request)
    {
        $clubId = $this->resolveClubId($hashedClubId);
        // $this->authorizeClub($clubId);
        
        if (!$clubId) return response()->json(['message' => 'Club tidak valid'], 400);

        $status = $request->input('status');
        if (!in_array($status, ['accepted', 'rejected'])) {
            return response()->json(['message' => 'Status tidak valid'], 400);
        }

        $requestData = DB::table('club_student_requests')
            ->where('id', $requestId)
            ->where('club_id', $clubId)
            ->first();

        if (!$requestData) {
            return response()->json(['message' => 'Request tidak ditemukan'], 404);
        }

        DB::table('club_student_requests')
            ->where('id', $requestId)
            ->update(['status' => $status, 'updated_at' => now()]);

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
        $clubId = $this->resolveClubId($hashedId);
        // $this->authorizeClub($clubId);
        
        if (!$clubId) {
            return response()->json(['message' => 'Ekskul tidak ditemukan (ID tidak valid)'], 404);
        }

        $members = DB::table('club_student')
            ->join('students', 'club_student.student_id', '=', 'students.id')
            ->leftJoin('jurusans', 'students.id_jurusan', '=', 'jurusans.id')
            ->where('club_student.club_id', $clubId)
            ->select(
                'club_student.id as club_student_id',
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
        $clubId = $this->resolveClubId($hashedClubId);
        // $this->authorizeClub($clubId);
        
        if (!$clubId) return response()->json(['message' => 'Club tidak valid'], 400);

        DB::table('club_student')
            ->where('club_id', $clubId)
            ->where('student_id', $studentId)
            ->delete();

        $updated = DB::table('club_student_requests')
            ->where('club_id', $clubId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['accepted', 'pending'])
            ->update(['status' => 'rejected', 'updated_at' => now()]);

        if ($updated) {
            return response()->json(['message' => 'Anggota berhasil dihapus & status diubah ke rejected']);
        } else {
            return response()->json(['message' => 'Status tidak bisa diubah karena data tidak ditemukan'], 404);
        }
    }
}
