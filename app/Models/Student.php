<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'nisn', 'kelas_id', 'phone', 'user_id', 'tanggal_lahir', 'alamat'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_student', 'student_id', 'club_id');
    }

    public function ledClubs()
    {
        return $this->hasMany(Club::class, 'student_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
