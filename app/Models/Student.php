<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'nisn', 'class', 'phone', 'user_id', 'id_jurusan', 'rombel', 'tanggal_lahir', 'alamat'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_student', 'student_id', 'club_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    protected $appends = ['kelas_lengkap'];

    public function getKelasLengkapAttribute()
    {
        return "{$this->class} {$this->jurusan->singkatan} {$this->rombel}";
    }
}
