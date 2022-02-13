<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruiterProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruiter_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('contact_email');
            $table->string('company_name');
            $table->string('logo_image_link')->nullable();
            // $table->string('description_image_link');
            $table->longText('description')->nullable();
            $table->string('phone_number');
            $table->boolean('verify')->nullable();
            $table->string('address');
            $table->unsignedInteger('company_size');
            $table->string('company_industry');
            $table->string('tax_code');
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('recruiter_profiles');
    }
}
