<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_enquiries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('course')->nullable();
            $table->string('other')->nullable();
            $table->string('ssc')->nullable();
            $table->string('hsc')->nullable();
            $table->string('graduation')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('student_no');
            $table->string('parent_no')->nullable();
            $table->string('land_line_no')->nullable();
            $table->string('reference_by')->nullable();
            $table->string('enquiry_by')->nullable();
            $table->integer('student_interest')->default(0);
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
        Schema::dropIfExists('student_enquiries');
    }
}
