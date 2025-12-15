<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubStudentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_student_requests', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('club_id');
    $table->unsignedBigInteger('student_id');
    $table->string('status')->default('pending'); // pending, accepted, rejected
    $table->timestamps();

    $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_student_requests');
    }
}
