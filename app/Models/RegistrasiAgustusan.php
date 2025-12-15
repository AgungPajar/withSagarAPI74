<?php

namespace App\Models;

use App\Models\Jurusan;
use App\Models\Student;
use App\Models\RegistrationMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistrasiAgustusan extends Model
{
    use HasFactory;

    protected $table = 'registrasi_agustusan';

    protected $fillable = ['nomor_hp', 'nama_tim', 'cabang_lomba'];

    public function members()
    {
        return $this->hasMany(RegistrationMember::class);
    }
}
