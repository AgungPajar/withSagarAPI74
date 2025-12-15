<?php

namespace App\Models;

use App\Models\Club;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityReport extends Model
{
    use HasFactory;
    protected $fillable = ['club_id', 'date', 'materi', 'tempat', 'photo_url'];
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
