<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantFamilyMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicant_family_members', function (Blueprint $table) {
            $table->id();
            $table->integer('applicant_id');
            $table->string('relationship')->nullable();
            $table->string('name')->nullable();
            $table->integer('age')->nullable();
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('occupation')->nullable();
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
        Schema::dropIfExists('applicant_family_members');
    }
}
