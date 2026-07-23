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
    public function index()
    {
        $clubs = Club::with('student')->orderBy('name', 'asc')->paginate(10);
        return view('administrator.ekskul.index', compact('clubs'));
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'description', 'group_link', 'student_id');

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clubs', 'public');
            $data['logo_path'] = $path;
        }

        Club::create($data);

        return redirect()->route('admin.ekskul.index')->with('success', 'Ekskul created successfully.');
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'description', 'group_link', 'student_id');

        if ($request->hasFile('logo')) {
            if ($ekskul->logo_path) {
                Storage::disk('public')->delete($ekskul->logo_path);
            }
            $path = $request->file('logo')->store('clubs', 'public');
            $data['logo_path'] = $path;
        }

        $ekskul->update($data);

        return redirect()->route('admin.ekskul.index')->with('success', 'Ekskul updated successfully.');
    }

    public function destroy(Club $ekskul)
    {
        if ($ekskul->logo_path) {
            Storage::disk('public')->delete($ekskul->logo_path);
        }
        $ekskul->delete();

        return redirect()->route('admin.ekskul.index')->with('success', 'Ekskul deleted successfully.');
    }
}
