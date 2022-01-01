<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_tags', function (Blueprint $table) {
            $table->id();
            $table->string('hashtags');
            $table->unsignedBigInteger('recruitment_id')->unique();
            $table->foreign('recruitment_id')->references('id')->on('recruitments')->onDelete('cascade');
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
        Schema::dropIfExists('job_tags');
    }
}
