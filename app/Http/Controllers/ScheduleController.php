<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class ScheduleController extends Controller
{
    public function index($clubHashedId)
    {
        $id = Hashids::decode($clubHashedId)[0] ?? null;
        $club = Club::findOrFail($id);

        return $club->schedules;
    }

    public function store(Request $request, $clubHashedId)
    {
        $id = Hashids::decode($clubHashedId)[0] ?? null;
        $club = Club::findOrFail($id);

        $validated = $request->validate([
            'day_of_week' => [
                'required',
                'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'unique:schedules,day_of_week,NULL,id,club_id,' . $club->id
            ],
        ]);

        $schedule = $club->schedules()->create($validated);
        return response()->json($schedule, 201);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->json(null, 204);
    }
}
