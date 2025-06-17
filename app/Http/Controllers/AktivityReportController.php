<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityReport;
use Vinkla\Hashids\Facades\Hashids;

class AktivityReportController extends Controller
{
    public function store(Request $request, $hashedId)
    {
        $decoded = Hashids::decode($hashedId);
        if (count($decoded) === 0) {
            return response()->json([
                'message' => 'Invalid ID provided.',
            ]);
        }

        $clubId = $decoded[0];
        $request->validate([
            'date' => 'required|date',
            'materi' => 'required|string',
            'tempat' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('foto_presensi', 'public');
        }

        $report = ActivityReport::create([
            'club_id' => $clubId,
            'date' => $request->date,
            'materi' => $request->materi,
            'tempat' => $request->tempat,
            'photo_url' => $photoPath,
        ]);
        
        return response()->json([
            'message'=> 'Berhasil disimpan',
            'has_photo' => $request->hasFile('photo'),
            'path' => $photoPath,
            'data' => $report,
        ], 200);
    }
}
