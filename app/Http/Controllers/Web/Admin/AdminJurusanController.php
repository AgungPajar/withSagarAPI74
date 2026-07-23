<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class AdminJurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::orderBy('urutan', 'asc')->paginate(10);
        return view('administrator.jurusan.index', compact('jurusans'));
    }

    public function create()
    {
        return view('administrator.jurusan.create');
    }

    public function show(Jurusan $jurusan)
    {
        $jurusan->load(['kelas' => function($query) {
            $query->withCount('students');
        }]);
        $jurusans = Jurusan::orderBy('urutan', 'asc')->get();
        return view('administrator.jurusan.show', compact('jurusan', 'jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'required|string|unique:jurusans,singkatan',
            'urutan' => 'nullable|integer',
        ]);

        Jurusan::create($request->only('nama', 'singkatan', 'urutan'));

        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan created successfully.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('administrator.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'required|string|unique:jurusans,singkatan,' . $jurusan->id,
            'urutan' => 'nullable|integer',
        ]);

        $jurusan->update($request->only('nama', 'singkatan', 'urutan'));

        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan updated successfully.');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'string|exists:jurusans,id',
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            Jurusan::where('id', $id)->update(['urutan' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan berhasil diperbarui.']);
    }
}
