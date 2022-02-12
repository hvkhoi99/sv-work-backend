<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('first_name')->nullable();
            $table->string('last_name');
            $table->string('avatar_link')->nullable();
            $table->dateTime('date_of_birth');
            $table->string('phone_number');
            $table->string('nationality');
            // $table->string('country');
            // $table->string('province_city');
            // $table->string('district');
            $table->string('address');
            $table->boolean('gender');
            $table->longText('over_view')->nullable();
            $table->boolean('open_for_job')->nullable();
            $table->string('job_title');
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('student_profiles');
    }
}
