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
            $table->string('contact_email')->nullable(); // new nullable
            $table->string('company_name')->nullable(); // new nullable
            $table->string('logo_image_link')->nullable();
            // $table->string('description_image_link');
            $table->longText('description')->nullable();
            $table->string('phone_number')->nullable(); // new nullable
            $table->boolean('verify')->nullable();
            $table->string('address')->nullable(); // new nullable
            $table->unsignedInteger('company_size')->nullable(); // new nullable
            $table->string('company_industry')->nullable(); // new nullable
            $table->string('tax_code')->nullable(); // new nullable
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
