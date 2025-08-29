<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Club extends Model
{
    use HasFactory;

    protected $table = 'clubs';
    protected $fillable = ['user_id', 'name', 'description', 'logo_path', 'group_link'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'club_student', 'club_id', 'student_id');
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class)->orderBy('day_of_week');
    }
}
