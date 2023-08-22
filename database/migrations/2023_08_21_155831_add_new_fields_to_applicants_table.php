<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->date('initial_interview_date')->nullable();
            $table->integer('initial_interview_status')->nullable();
            $table->string('position_preference')->nullable();
            $table->string('branch_preference')->nullable();
            $table->integer('iq_status')->nullable();
            $table->integer('bi_status')->nullable();
            $table->date('final_interview_date')->nullable();
            $table->integer('final_interview_status')->nullable();
            $table->integer('employment_position')->nullable();
            $table->integer('employment_branch')->nullable();
            $table->date('orientation_date')->nullable();
            $table->date('signing_of_contract_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('initial_interview_date');
            $table->dropColumn('initial_interview_status');
            $table->dropColumn('position_preference');
            $table->dropColumn('branch_preference');
            $table->dropColumn('iq_status');
            $table->dropColumn('bi_status');
            $table->dropColumn('final_interview_date');
            $table->dropColumn('final_interview_status');
            $table->dropColumn('employment_position');
            $table->dropColumn('employment_branch');
            $table->dropColumn('orientation_date');
            $table->dropColumn('signing_of_contract_date')  ;
        });
    }
}
