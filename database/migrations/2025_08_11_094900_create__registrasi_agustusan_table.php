<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrasiAgustusanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrasi_agustusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('id_jurusan')->constrained('jurusans')->onDelete('cascade')->nullable();
            $table->string('cabang_lomba')->nullable();
            $table->string('nama_tim')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registrasi_agustusan');
    }
}
