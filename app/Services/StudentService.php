<?php

namespace App\Services;

use App\Models\Club;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentService
{
    public function getFilteredStudents(array $filters)
    {
        $query = Student::with('jurusan', 'clubs');

        if (isset($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (isset($filters['jurusan'])) {
            $query->whereHas('jurusan', function ($q) use ($filters) {
                $q->where('singkatan', $filters['jurusan']);
            });
        }

        // Pindahkan join dan order ke sini
        return $query->join('jurusans', 'students.id_jurusan', '=', 'jurusans.id')
            ->orderBy('jurusans.id')
            ->orderByRaw('CAST(rombel AS UNSIGNED)')
            ->orderBy('students.name')
            ->select('students.*')
            ->get();
    }

    public function updateStudentProfile(User $user, array $data): void
    {
        $user->name = $data['name'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $student = $user->student;
        $student->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'alamat' => $data['alamat'],
        ]);
    }

    public function deleteStudent(Student $student): void
    {
        $student->clubs()->detach();
        if ($student->user) {
            $student->user->delete();
        }
        $student->delete();
    }

    public function addStudentToClub(array $data, string $clubHashedId): Student
    {
        $clubId = Hashids::decode($clubHashedId)[0] ?? null;
        if (!$clubId) {
            throw new \Exception("Invalid club ID");
        }

        // Logic find or create student
        $student = Student::updateOrCreate(
            ['nisn' => $data['nisn']],
            [
                'name' => $data['name'],
                'class' => $data['class'] ?? '',
                'phone' => $data['phone'],
                'alamat' => $data['alamat'],
                'user_id' => auth()->id(), // Asumsi user yang nambahin
            ]
        );

        $student->clubs()->syncWithoutDetaching([$clubId]);
        return $student;
    }

    public function getStudentDashboardData(string $studentHashedId): array
    {
        $studentId = Hashids::decode($studentHashedId)[0] ?? null;
        $student = Student::find($studentId);

        if (!$student) {
            throw new \Exception("Student not found");
        }

        $clubs = Club::all()->map(function ($club) use ($studentId) {
            $club->hash_id = Hashids::encode($club->id);
            $status = DB::table('club_student_requests')
                ->where('club_id', $club->id)
                ->where('student_id', $studentId)
                ->value('status');
            $club->status = $status;
            return $club;
        });

        return ['student' => $student, 'clubs' => $clubs];
    }

    public function importStudentsFromExcel(UploadedFile $file): void
    {
        $rows = IOFactory::load($file->getRealPath())->getActiveSheet()->toArray();

        // Mulai dari baris kedua (index 1) untuk skip header
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $nisn = $row[2];
            $nama = $row[1];

            if (!$nisn || !$nama) continue;

            DB::transaction(function () use ($row, $nama, $nisn) {
                $student = Student::create([
                    'name'       => $nama,
                    'nisn'       => $nisn,
                    'class'      => $row[3],
                    'id_jurusan' => $row[4],
                    'rombel'     => $row[5] ?? 1,
                ]);

                $user = User::create([
                    'name'     => $nama,
                    'username' => $nisn,
                    'password' => Hash::make('password12345'), // Default password
                    'role'     => 'student',
                ]);

                $student->update(['user_id' => $user->id]);
            });
        }
    }

    public function generateImportTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Jurusan (id_jurusan)');
        $sheet->setCellValue('F1', 'Rombel');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_siswa.xlsx"',
        ]);
    }

    public function promoteStudents(array $data): void
    {
        Student::whereIn('id', $data['ids'])->update(['class' => $data['class']]);
    }

    public function getStudentsByClass(string $class)
    {
        return Student::with('jurusan')->where('class', $class)->get();
    }

    public function deleteMultipleStudents(array $ids): void
    {
        // Hapus user terkait dulu
        $userIds = Student::whereIn('id', $ids)->whereNotNull('user_id')->pluck('user_id');
        User::whereIn('id', $userIds)->delete();

        // Hapus siswa
        Student::whereIn('id', $ids)->delete();
    }
}
