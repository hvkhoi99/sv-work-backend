<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('message_id');
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            // $table->unsignedBigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('r_profile_id')->nullable();
            $table->foreign('r_profile_id')->references('id')->on('recruiter_profiles')->onDelete('cascade');
            $table->unsignedBigInteger('s_profile_id')->nullable();
            $table->foreign('s_profile_id')->references('id')->on('student_profiles')->onDelete('cascade');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->boolean('is_read')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_messages');
    }
}
