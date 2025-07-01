<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        DB::table('jurusans')->insert($jurusans);
    }
}
