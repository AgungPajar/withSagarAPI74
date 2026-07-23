<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Kelas extends Model
{
    use HasFactory, HasUuids, HasSlug;

    protected $fillable = [
        'nama',
        'slug',
        'jurusan_id',
        'tingkatan',
        'rombel',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('nama')
            ->saveSlugsTo('slug');
    }
}
