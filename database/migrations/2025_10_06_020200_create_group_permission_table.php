<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPermissionTable extends Migration
{
    public function up()
    {
        Schema::create('group_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('user_groups')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_permission');
    }
}
