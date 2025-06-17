<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityReport extends Model
{
    use HasFactory;
    protected $fillable = ['club_id', 'date', 'materi', 'tempat', 'photo_url'];
}
