<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); 
            $table->longText('description');
            $table->string('location');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->string('image_link')->nullable();
            $table->boolean('is_closed')->nullable();
            // $table->unsignedBigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('r_profile_id')->nullable();
            $table->foreign('r_profile_id')->references('id')->on('recruiter_profiles')->onDelete('cascade');
            $table->unsignedBigInteger('s_profile_id')->nullable();
            $table->foreign('s_profile_id')->references('id')->on('student_profiles')->onDelete('cascade');
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
        Schema::dropIfExists('events');
    }
}
