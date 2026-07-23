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
        }]);
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
        $importId = (string) Str::uuid();
        
        // Initial state in cache
        Cache::put('import_status_' . $importId, [
            'status' => 'pending',
            'processed' => 0,
            'total' => 0,
            'error' => null,
            'updated_at' => now()->timestamp,
        ], now()->addHours(2));
        
        // Store import ID in session to track it across pages
        $activeImports = session()->get('active_imports', []);
        $activeImports[] = $importId;
        session()->put('active_imports', $activeImports);

        // Dispatch Job
        ProcessSiswaImport::dispatch($fullPath, $kela->id, $importId);

        return redirect()->route('admin.kelas.show', $kela->id)
            ->with('success', 'Import file is processing in the background. You can navigate to other pages.');
    }

    public function importStatus(Request $request)
    {
        $activeImports = session()->get('active_imports', []);
        $statuses = [];
        $completedImports = [];

        foreach ($activeImports as $importId) {
            $status = Cache::get('import_status_' . $importId);
            if ($status) {
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
