<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Student::with('club')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated  = $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'club_id' => 'required|exists:clubs,id',
        ]);

        $student = Student::create($validated);
        return response()->json($student, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $hashedId)
    {
        $decoded = Hashids::decode($hashedId);
        if (count($decoded) === 0) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $id = $decoded[0];

        $user = $request->user();
        if ($user->role !== 'student' || !$user->student || $user->student->id !== $id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $student = Student::with('jurusan', 'clubs')->find($id);
        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        return response()->json([
            'name' => $student->name,
            'nisn' => $student->nisn,
            'class' => $student->class,
            'jurusan' => $student->jurusan->nama ?? null,
            'phone' => $student->phone,
            'birthdate' => $student->birthdate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, student $student)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'nisn' => 'sometimes|required|string|max:20|unique:students,nisn,' . $student->id,
            'club_id' => 'sometimes|required|exists:clubs,id',
        ]);

        $student->update($validated);
        return response()->json($student);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->clubs()->detach();
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function storeToClub(Request $request, $hashedId)
    {
        $decoded = Hashids::decode($hashedId);

        if (count($decoded) === 0) {
            return response()->json(['message' => 'Invalid club ID'], 404);
        }

        $clubId = $decoded[0];

        $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|max:20',
            'class' => 'required|string|max:50',
            'phone' => 'required|string|max:50',
        ]);

        $student = Student::where('nisn', $request->nisn)->first();

        if (!$student) {
            $student = Student::create([
                'name' => $request->name,
                'nisn' => $request->nisn,
                'class' => $request->class ?? '',
                'phone' => $request->phone,
            ]);
        } else {
            // Update nama dan kelas jika kosong atau berubah
            $student->update([
                'name' => $request->name,
                'class' => $request->class ?? $student->class,
                'phone' => $request->phone,
            ]);
        }

        $student->clubs()->syncWithoutDetaching([$clubId]);

        return response()->json([
            'message' => 'Student added to club successfully',
            'student' => $student,
        ]);
    }

    public function dashboard($hashId)
    {
        $decoded = Hashids::decode($hashId);
        if (count($decoded) === 0) {
            return response()->json(['message' => 'Invalid hash ID'], 404);
        }

        $studentId = $decoded[0];
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Ambil semua clubs
        $clubs = Club::all()->map(function ($club) use ($studentId) {
            $club->hash_id = Hashids::encode($club->id);

            $status = DB::table('club_student_requests')
                ->where('club_id', $club->id)
                ->where('student_id', $studentId)
                ->value('status');

            $club->status = $status ?? null;
            return $club;
        });

        return response()->json([
            'student' => $student,
            'clubs' => $clubs
        ]);
    }

    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
            ]);

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                $nama    = $row[1];
                $nisn    = $row[2];
                $kelas   = $row[3];
                $jurusan = $row[4];
                $rombel = $row[5];

                if (!$nisn || !$nama || !$kelas || !$jurusan || !$rombel) continue;

                $student = Student::create([
                    'name'       => $nama,
                    'nisn'       => $nisn,
                    'class'      => $kelas,
                    'id_jurusan' => $jurusan,
                    'rombel' => $rombel,
                ]);

                User::create([
                    'name'       => $nama,
                    'username'   => $nisn,
                    'password'   => Hash::make('password12345'),
                    'student_id' => $student->id,
                ]);
            }

            return response()->json(['message' => 'Data berhasil diimpor.']);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal import: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Jurusan (id_jurusan)');
        $sheet->setCellValue('F1', 'rombel');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);

        // kirim langsung via stream
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_siswa.xlsx"',
        ]);
    }

    public function promote(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'class' => 'required|in:X,XI,XII,LULUS',
        ]);

        Student::whereIn('id', $request->ids)
            ->update(['class' => $request->class]);

        return response()->json(['message' => 'Siswa berhasil dinaikkan.']);
    }


    public function getByClass($class)
    {
        $students = Student::with('jurusan')->where('class', $class)->get();
        return response()->json($students);
    }

    public function indexStudents(Request $request)
    {
        $query = Student::with('jurusan', 'clubs');

        if ($request->has('class')) {
            $query->where('class', $request->class);
        }

        if ($request->has('jurusan')) {
            $query->whereHas('jurusan', function ($q) use ($request) {
                $q->where('singkatan', $request->jurusan);
            });
        }

        // Order by jurusan.id, rombel (as integer), then name alphabetically
        $query->join('jurusans', 'students.id_jurusan', '=', 'jurusans.id')
            ->orderBy('jurusans.id')
            ->orderByRaw('CAST(rombel AS UNSIGNED)')
            ->orderBy('students.name');

        $students = $query->select('students.*')->get();

        return response()->json($students);
    }
}
