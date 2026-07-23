<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        return response()->json(Jurusan::all());
    }

    public function getKelas(Request $request)
    {
        $query = \App\Models\Kelas::query();
        if ($request->has('tingkatan')) {
            $query->where('tingkatan', $request->tingkatan);
        }
        if ($request->has('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }
        return response()->json($query->orderBy('nama', 'asc')->get(['id', 'nama', 'tingkatan', 'jurusan_id', 'rombel']));
    }
}
