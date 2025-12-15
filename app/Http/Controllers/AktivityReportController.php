<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityReport;
use Vinkla\Hashids\Facades\Hashids;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Hash;

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

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
        if ($request->hasFile('photo')) {
            $uploadedFileUrl = $cloudinary->uploadApi()->upload($request->file('photo')->getRealPath(), [
                'folder' => 'presensi-ekskul',
                'resource_type' => 'image',
                'verify' => false,
            ]);

            $photoPath = $uploadedFileUrl['secure_url'] ?? null;
        }

        $report = ActivityReport::create([
            'club_id' => $clubId,
            'date' => $request->date,
            'materi' => $request->materi,
            'tempat' => $request->tempat,
            'photo_url' => $photoPath,
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'has_photo' => $request->hasFile('photo'),
            'path' => $photoPath,
            'data' => $report,
        ], 200);
    }

    public function getByClub($hashedId)
    {
        $decoded = Hashids::decode($hashedId);

        if (count($decoded) === 0) {
            return response()->json(['message' => 'Invalid club ID'], 404);
        }

        $clubId = $decoded[0];

        $reports = ActivityReport::where('club_id', $clubId)->get();

        return response()->json($reports);
    }

    public function getAll(){
        $reports = ActivityReport::with('club')->latest()->get();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil ambil semua laporan',
            'data' => $reports,
        ]);
    }
}
