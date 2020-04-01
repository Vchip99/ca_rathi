<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `batches` ADD INDEX `idx_course_id` (`course_id`)');
        DB::statement('ALTER TABLE `batches` ADD INDEX `idx_sub_course_id` (`sub_course_id`)');
        DB::statement('ALTER TABLE `course_payments` ADD INDEX `idx_course_id` (`course_id`)');
        DB::statement('ALTER TABLE `course_payments` ADD INDEX `idx_sub_course_id` (`sub_course_id`)');
        DB::statement('ALTER TABLE `course_payments` ADD INDEX `idx_batch_id` (`batch_id`)');
        DB::statement('ALTER TABLE `course_payments` ADD INDEX `idx_fee_type` (`fee_type`)');
        DB::statement('ALTER TABLE `course_payments` ADD INDEX `idx_payment_type` (`payment_type`)');
        DB::statement('ALTER TABLE `course_payments` ADD INDEX `idx_course_payment_type` (`course_payment_type`)');
        DB::statement('ALTER TABLE `sub_courses` ADD INDEX `idx_course_id` (`course_id`)');
        DB::statement('ALTER TABLE `user_courses` ADD INDEX `idx_course_id` (`course_id`)');
        DB::statement('ALTER TABLE `user_courses` ADD INDEX `idx_sub_course_id` (`sub_course_id`)');
        DB::statement('ALTER TABLE `user_courses` ADD INDEX `idx_batch_id` (`batch_id`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX `idx_course_id` ON `batches`');
        DB::statement('DROP INDEX `idx_sub_course_id` ON `batches`');
        DB::statement('DROP INDEX `idx_course_id` ON `course_payments`');
        DB::statement('DROP INDEX `idx_sub_course_id` ON `course_payments`');
        DB::statement('DROP INDEX `idx_batch_id` ON `course_payments`');
        DB::statement('DROP INDEX `idx_fee_type` ON `course_payments`');
        DB::statement('DROP INDEX `idx_payment_type` ON `course_payments`');
        DB::statement('DROP INDEX `idx_course_payment_type` ON `course_payments`');
        DB::statement('DROP INDEX `idx_course_id` ON `sub_courses`');
        DB::statement('DROP INDEX `idx_course_id` ON `user_courses`');
        DB::statement('DROP INDEX `idx_sub_course_id` ON `user_courses`');
        DB::statement('DROP INDEX `idx_batch_id` ON `user_courses`');
    }
}
