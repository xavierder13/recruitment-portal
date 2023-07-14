<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantEducAttainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicant_educ_attains', function (Blueprint $table) {
            $table->id();
            $table->integer('applicant_id');
            $table->string('educ_level')->nullable();
            $table->string('school')->nullable();
            $table->string('course')->nullable();
            $table->string('major')->nullable();
            $table->string('sy_attended')->nullable();
            $table->string('honors')->nullable();
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
        Schema::dropIfExists('applicant_educ_attains');
    }
}
