<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminClubController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->perPage, [10, 25, 50]) ? (int) $request->perPage : 10;
        $search = $request->search;

        $clubs = Club::with('student')
            ->orderBy('urutan', 'asc')
            ->when($search, function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhereHas('student', function($q2) use ($search) {
                      $q2->where('name', 'ilike', "%{$search}%");
                  });
            })
            ->paginate($perPage)
            ->withQueryString();
        $students = Student::orderBy('name', 'asc')->get();

        return view('administrator.ekskul.index', compact('clubs', 'students'));
    }

    public function create()
    {
        $students = Student::orderBy('name', 'asc')->get();
        return view('administrator.ekskul.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_link' => 'nullable|url',
            'student_id' => 'nullable|exists:students,id',
            'urutan' => 'nullable|integer',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'description', 'group_link', 'student_id', 'urutan');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('logos', $filename, 'public');
            $data['logo_path'] = $filename;
        }

        Club::create($data);

        return redirect()->route('admin.ekskul.index')->with('success', 'Ekskul created successfully.');
    }

    public function show(Club $ekskul)
    {
        $ekskul->load(['student', 'students.kelas']);
        
        $pendaftar = \Illuminate\Support\Facades\DB::table('club_student_requests')
            ->join('students', 'club_student_requests.student_id', '=', 'students.id')
            ->leftJoin('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->where('club_student_requests.club_id', $ekskul->id)
            ->where('club_student_requests.status', 'pending')
            ->select('students.*', 'kelas.nama as kelas_nama', 'club_student_requests.created_at as request_date')
            ->get();

        return view('administrator.ekskul.show', compact('ekskul', 'pendaftar'));
    }

    public function edit(Club $ekskul)
    {
        $students = Student::orderBy('name', 'asc')->get();
        return view('administrator.ekskul.edit', compact('ekskul', 'students'));
    }

    public function update(Request $request, Club $ekskul)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_link' => 'nullable|url',
            'student_id' => 'nullable|exists:students,id',
            'urutan' => 'nullable|integer',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'description', 'group_link', 'student_id', 'urutan');

        if ($request->hasFile('logo')) {
            if ($ekskul->logo_path) {
                $oldFile = str_replace('logos/', '', $ekskul->logo_path);
                Storage::disk('public')->delete('logos/' . $oldFile);
            }
            $file = $request->file('logo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('logos', $filename, 'public');
            $data['logo_path'] = $filename;
        }

        $ekskul->update($data);

        return redirect()->back()->with('success', 'Ekskul updated successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|exists:clubs,id',
        ]);

        foreach ($request->order as $index => $id) {
            Club::where('id', $id)->update(['urutan' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan ekskul berhasil diperbarui.']);
    }

    public function destroy(Club $ekskul)
    {
        if ($ekskul->logo_path) {
            $oldFile = str_replace('logos/', '', $ekskul->logo_path);
            Storage::disk('public')->delete('logos/' . $oldFile);
        }
        $ekskul->delete();

        return redirect()->back()->with('success', 'Ekskul deleted successfully.');
    }

    public function bulkDeleteMembers(Request $request, Club $ekskul)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        \Illuminate\Support\Facades\DB::table('club_student')
            ->where('club_id', $ekskul->id)
            ->whereIn('student_id', $request->student_ids)
            ->delete();

        \Illuminate\Support\Facades\DB::table('club_student_requests')
            ->where('club_id', $ekskul->id)
            ->whereIn('student_id', $request->student_ids)
            ->whereIn('status', ['accepted', 'pending'])
            ->update(['status' => 'rejected', 'updated_at' => now()]);

        return redirect()->back()->with('success', count($request->student_ids) . ' anggota berhasil dihapus.');
    }

    public function bulkDeleteRequests(Request $request, Club $ekskul)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        \Illuminate\Support\Facades\DB::table('club_student_requests')
            ->where('club_id', $ekskul->id)
            ->whereIn('student_id', $request->student_ids)
            ->where('status', 'pending')
            ->delete();

        return redirect()->back()->with('success', count($request->student_ids) . ' pendaftar berhasil dihapus.');
    }
}
