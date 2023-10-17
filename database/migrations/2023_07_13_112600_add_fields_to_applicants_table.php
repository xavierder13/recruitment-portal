<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('address2');
            $table->string('birth_place');
            $table->string('citizenship');
            $table->string('religion');
            $table->decimal('weight', 12, 2);
            $table->decimal('height', 12, 2);
            $table->string('sss_no')->nullable();
            $table->string('philhealth_no')->nullable();
            $table->string('pagibig_no')->nullable();
            $table->string('tin_no')->nullable();
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
            $table->dropColumn('birth_place');
            $table->dropColumn('citizenship');
            $table->dropColumn('religion');
            $table->dropColumn('weight');
            $table->dropColumn('height');
            $table->dropColumn('sss_no');
            $table->dropColumn('philhealth_no');
            $table->dropColumn('pagibig_no');
            $table->dropColumn('tin_no');
        });
    }
}
