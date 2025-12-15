<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupUserTable extends Migration
{
    public function up()
    {
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('user_groups')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type')->default('user'); // 'user' or 'admin'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_user');
    }
}
