<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Student;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'jurusan' => Jurusan::count(),
            'kelas' => Kelas::count(),
            'siswa' => Student::count(),
        ];
        
        return view('administrator.dashboard.index', compact('stats'));
    }
}
