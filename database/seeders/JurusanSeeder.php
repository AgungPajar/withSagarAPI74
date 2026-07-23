<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;
use App\Models\Kelas;

class JurusanSeeder extends Seeder
{
    public function run()
    {
        $jurusans = [
            ['nama' => 'Akuntansi Keuangan dan Lembaga', 'singkatan' => 'AKL'],
            ['nama' => 'Manajemen Perkantoran dan Layanan Bisnis', 'singkatan' => 'MPL'],
            ['nama' => 'Pemasaran', 'singkatan' => 'PM'],
            ['nama' => 'Teknik Jaringan dan Komputer', 'singkatan' => 'TJK'],
            ['nama' => 'Desain Komunikasi Visual', 'singkatan' => 'DKV'],
            ['nama' => 'Pengembangan Perangkat Lunak dan Gim', 'singkatan' => 'PPL'],
            ['nama' => 'Teknologi Laboratorium Medik', 'singkatan' => 'TLM'],
            ['nama' => 'Teknik Farmasi', 'singkatan' => 'TKF'],
            ['nama' => 'Teknik Logistik', 'singkatan' => 'TLG'],
            ['nama' => 'Teknik Energi Terbarukan', 'singkatan' => 'TET'],
        ];

        foreach ($jurusans as $j) {
            $jurusan = Jurusan::create($j);
            
            // Create some default classes for each Jurusan
            Kelas::create(['jurusan_id' => $jurusan->id, 'nama' => 'X ' . $j['singkatan'] . ' 1']);
            Kelas::create(['jurusan_id' => $jurusan->id, 'nama' => 'XI ' . $j['singkatan'] . ' 1']);
            Kelas::create(['jurusan_id' => $jurusan->id, 'nama' => 'XII ' . $j['singkatan'] . ' 1']);
        }
    }
}
