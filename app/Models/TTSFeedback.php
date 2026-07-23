<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TTSFeedback extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'tts_feedback'; 
    protected $fillable = ['name', 'message'];
}
