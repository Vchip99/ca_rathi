<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receipt_id');
            $table->string('f_name');
            $table->string('m_name');
            $table->string('l_name');
            $table->integer('user_id');
            $table->string('phone');
            $table->integer('course_id');
            $table->integer('sub_course_id');
            $table->integer('batch_id');
            $table->tinyInteger('fee_type');
            $table->tinyInteger('payment_type');
            $table->string('cheque_no')->nullable();
            $table->string('amount');
            $table->string('all_name')->nullable();
            $table->string('ram_rathi')->nullable();
            $table->string('shyam_rathi')->nullable();
            $table->string('giridhar_rathi')->nullable();
            $table->string('dipti_rathi')->nullable();
            $table->string('sunita_rathi')->nullable();
            $table->string('remainder_date')->nullable();
            $table->text('comment')->nullable();
            $table->integer('course_payment_type');
            $table->integer('generated_by');
            $table->tinyInteger('is_deleted')->default(0);
            $table->tinyInteger('allow_to_visible')->default(1);
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
        Schema::dropIfExists('course_payments');
    }
}
