<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request) {
        $hashId = $request->query('club_id');
        $decoded = [$hashId];

        if (count($decoded) === 0) {
            return response()->json([
                'message' => 'Invalid club ID',
            ], 400);
        }

        $clubId = $decoded[0];
        $date = $request->query('date');

        $rekap = Attendance::with(['student.jurusan'])
            ->where('club_id', $clubId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->get();
        
        return response()->json($rekap);
    }
}
