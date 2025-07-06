<?php

namespace App\Models;

use App\Models\Jurusan;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['student_id', 'club_id', 'status', 'date'];

    public function getTable(){
        return 'attendances';
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function jurusan()
{
    return $this->belongsTo(Jurusan::class, 'id_jurusan');
}

}
