<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class RekapController extends Controller
{
    public function index(Request $request) {
        $hashId = $request->query('club_id');
        $decoded = Hashids::decode($hashId);

        if (count($decoded) === 0) {
            return response()->json([
                'message' => 'Invalid club ID',
            ], 400);
        }

        $clubId = $decoded[0];
        $date = $request->query('date');

        $rekap = Attendance::with('student')
            ->where('club_id', $clubId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->get();
        
        return response()->json($rekap);
    }
}
