<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Attendance::all());
    }

    public function store(Request $request)
    {
        $data = $request->input('data');
        
        if (!is_array($data)) {
            return response()->json([
                'message' => 'Format data tidak sesuai',
            ], 422);
        }

        foreach ($data as $index => $item) {
            if (!is_numeric($item['club_id'])) {
                $decoded = Hashids::decode($item['club_id']);
                if (empty($decoded)) {
                    return response()->json([
                        'message' => 'ID klub tidak valid pada item ke-' . ($index + 1),
                    ], 422);
                }
                $data[$index]['club_id'] = $decoded[0];
                $item['club_id'] = $decoded[0];
            };

            $validator = Validator::make($item, [
                'student_id'=> 'required|exists:students,id',
                'club_id' => 'required|exists:clubs,id',
                // 'status' => 'required|in:hadir,izin,sakit,alfa',
                'status' => 'required|in:hadir,tidak hadir',
                'date' => 'required|date',
            ]);

            if ($validator->fails()){
                return response()->json([
                    'message' => "Format data salah di indeks {$index}",
                    'errors' => $validator->errors(),
                ], 422);
            }

            Attendance::create($data[$index]);
        }
    }

    public function show(Attendance $attendance)
    {
        return response()->json($attendance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->Validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,tidak hadir',
        ]);

        $attendance->update($validated);
        return response()->json([
            $attendance
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json([
            null, 204
        ]);
    }
}
