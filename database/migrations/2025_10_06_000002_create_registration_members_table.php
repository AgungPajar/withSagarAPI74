<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('registration_members')) {
            Schema::create('registration_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('registrasi_agustusan_id')->constrained('registrasi_agustusan')->onDelete('cascade');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('id_jurusan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_members');
    }
}
