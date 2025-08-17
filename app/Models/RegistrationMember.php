<?php

namespace App\Models;

use App\Models\Jurusan;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistrationMember extends Model
{
    protected $fillable = ['registrasi_agustusan_id', 'student_id', 'id_jurusan'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }
}
