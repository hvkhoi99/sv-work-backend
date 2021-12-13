<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_profile_id');
            $table->foreign('r_profile_id')->references('id')->on('recruiter_profiles')->onDelete('cascade');
            $table->unsignedBigInteger('s_profile_id');
            $table->foreign('s_profile_id')->references('id')->on('student_profiles')->onDelete('cascade');
            $table->boolean('is_followed');
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
        Schema::dropIfExists('follows');
    }
}
