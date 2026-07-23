<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Jobs\ProcessSiswaImport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AdminKelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::select('kelas.*')
            ->join('jurusans', 'kelas.jurusan_id', '=', 'jurusans.id')
            ->orderBy('jurusans.urutan', 'asc')
            ->orderBy('kelas.nama', 'asc')
            ->with('jurusan')
            ->withCount('students')
            ->paginate(10);
        $jurusans = Jurusan::orderBy('urutan', 'asc')->get();
        return view('administrator.kelas.index', compact('kelas', 'jurusans'));
    }

    public function create()
    {
        $jurusans = Jurusan::orderBy('urutan', 'asc')->get();
        return view('administrator.kelas.create', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkatan' => 'required|in:X,XI,XII',
            'jurusan_id' => 'required|exists:jurusans,id',
            'rombel' => 'required|string|max:50',
        ]);

        $jurusan = Jurusan::findOrFail($request->jurusan_id);
        $nama = $request->tingkatan . ' ' . $jurusan->singkatan . ' ' . $request->rombel;

        Kelas::create([
            'nama' => $nama,
            'jurusan_id' => $request->jurusan_id,
            'tingkatan' => $request->tingkatan,
            'rombel' => $request->rombel,
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas created successfully.');
    }

    public function show(Kelas $kela)
    {
        $kelas = $kela->load(['jurusan', 'students' => function($q) {
            $q->orderBy('name', 'asc');
        }])->loadCount('students');
        return view('administrator.kelas.show', compact('kelas'));
    }

    public function importSiswa(Request $request, Kelas $kela)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('imports', $filename);
        
        $fullPath = storage_path('app/' . $path);
        
        try {
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $totalRows = count($rows) - 1; // subtract header
            if ($totalRows <= 0) {
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                return redirect()->route('admin.kelas.show', $kela->id)->with('error', 'File excel kosong atau tidak valid.');
            }

            $processedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index == 0) continue; // Skip header

                $nisn = $row[0] ?? null;
                $name = $row[1] ?? null;

                if ($nisn && $name) {
                    $user = User::where('username', $nisn)->first();
                    if (!$user) {
                        $user = User::create([
                            'username' => $nisn,
                            'name' => $name,
                            'role' => 'student',
                            'password' => bcrypt('ossagar123'),
                        ]);
                    } else {
                        $user->update([
                            'name' => $name,
                            'role' => 'student',
                        ]);
                    }

                    Student::updateOrCreate(
                        ['nisn' => $nisn],
                        [
                            'name' => $name,
                            'kelas_id' => $kela->id,
                            'user_id' => $user->id,
                        ]
                    );
                    $processedCount++;
                }
            }

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return redirect()->route('admin.kelas.show', $kela->id)->with('success', "Import Siswa berhasil! {$processedCount} data diproses.");
            
        } catch (\Throwable $e) {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            return redirect()->route('admin.kelas.show', $kela->id)->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importStatus(Request $request)
    {
        $activeImports = session()->get('active_imports', []);
        $statuses = [];
        $completedImports = [];

        foreach ($activeImports as $importId) {
            $status = Cache::get('import_status_' . $importId);
            if ($status) {
                $updatedAt = $status['updated_at'] ?? 0;
                // If job hasn't updated for 5 minutes, mark as failed (stuck)
                if (in_array($status['status'], ['pending', 'processing']) && (time() - $updatedAt) > 300) {
                    $status['status'] = 'failed';
                    $status['error'] = 'Proses terhenti (Queue timeout / error).';
                    Cache::put('import_status_' . $importId, $status, now()->addHours(1));
                }

                $statuses[$importId] = $status;
                if (in_array($status['status'], ['completed', 'failed'])) {
                    $completedImports[] = $importId;
                }
            } else {
                $completedImports[] = $importId; // Remove if cache expired
            }
        }

        // Clean up completed imports from session
        if (!empty($completedImports)) {
            $activeImports = array_diff($activeImports, $completedImports);
            session()->put('active_imports', $activeImports);
        }

        return response()->json(['imports' => $statuses]);
    }

    public function edit(Kelas $kela) // Note parameter name might be bound as $kela due to pluralization
    {
        $kelas = $kela; // Laravel binds it as $kela for 'kelas'
        $jurusans = Jurusan::orderBy('urutan', 'asc')->get();
        return view('administrator.kelas.edit', compact('kelas', 'jurusans'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $kelas = $kela;
        $request->validate([
            'nama' => 'required|string|max:255',
            'tingkatan' => 'required|in:X,XI,XII',
            'jurusan_id' => 'required|exists:jurusans,id',
            'rombel' => 'required|string|max:50',
        ]);

        $kelas->update([
            'nama' => $request->nama,
            'jurusan_id' => $request->jurusan_id,
            'tingkatan' => $request->tingkatan,
            'rombel' => $request->rombel,
        ]);

        return redirect()->back()->with('success', 'Kelas updated successfully.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return redirect()->back()->with('success', 'Kelas deleted successfully.');
    }
}
