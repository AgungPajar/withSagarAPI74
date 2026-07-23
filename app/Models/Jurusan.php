<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Jurusan extends Model
{
    use HasFactory, HasUuids, HasSlug;

    protected $table = 'jurusans';

    protected $fillable = ['nama', 'singkatan', 'slug', 'urutan'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('nama')
            ->saveSlugsTo('slug');
    }
}
