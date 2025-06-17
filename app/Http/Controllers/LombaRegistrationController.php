<?php

namespace App\Http\Controllers;

use App\Models\LombaRegistration;
use Illuminate\Http\Request;

class LombaRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = LombaRegistration::all();
        return response()->json($data);
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
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:50',
            'nomor_hp' => 'required|string|max:15',
            'cabang_olahraga' => 'required|string|max:100',
            'nama_tim' => 'nullable|string|max:100',
        ]);

        LombaRegistration::create($validated);

        return response()->json(['message' => 'Pendaftaran berhasil'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LombaRegistration  $lombaRegistration
     * @return \Illuminate\Http\Response
     */
    public function show(LombaRegistration $lombaRegistration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LombaRegistration  $lombaRegistration
     * @return \Illuminate\Http\Response
     */
    public function edit(LombaRegistration $lombaRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LombaRegistration  $lombaRegistration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:50',
            'nomor_hp' => 'required|string|max:15',
            'cabang_olahraga' => 'required|string|max:100',
            'nama_tim' => 'nullable|string|max:100',
        ]);

        $registration = LombaRegistration::find($id);
        $registration->update($validated);

        return response()->json([
            'message'=> 'Data berhasil di update'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LombaRegistration  $lombaRegistration
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $registration = LombaRegistration::find($id);
        $registration->delete();

        return response()->json([
            'message'=> 'Data berhasil dihapus'
        ]);
    }
}
