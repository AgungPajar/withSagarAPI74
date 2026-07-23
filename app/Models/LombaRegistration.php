<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LombaRegistration extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'nama',
        'kelas',
        'nomor_hp',
        'cabang_olahraga',
        'nama_tim',
    ];
}
