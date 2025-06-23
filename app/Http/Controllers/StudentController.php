<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

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
    public function show(student $student)
    {
        //
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
}
