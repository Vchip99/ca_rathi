<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id');
            $table->integer('sub_course_id');
            $table->string('name');
            $table->string('fee');
            $table->string('gst');
            $table->string('receipt_by')->nullable();
            $table->string('gstin')->nullable();
            $table->string('cin')->nullable();
            $table->string('pan')->nullable();
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
        Schema::dropIfExists('batches');
    }
}