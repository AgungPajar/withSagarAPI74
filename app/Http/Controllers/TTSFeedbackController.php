<?php

namespace App\Http\Controllers;

use App\Models\TTSFeedback;
use Illuminate\Http\Request;

class TTSFeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = TTSFeedback::latest()->get(); // bisa pakai paginate juga
        return response()->json($feedbacks);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:500',
            'message' => 'required|string|max:10000',
        ]);

        TTSFeedback::create($validated);

        return response()->json(['message' => 'Pesan Berhasil Disimpan'], 201);
    }
}
