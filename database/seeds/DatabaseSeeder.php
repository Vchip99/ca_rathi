<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('courses')->insert([
            ['id' => 1,'name' => 'CA Intermediate','created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 2,'name' => 'CA Foundation','created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 3,'name' => '11th - 12th','created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
        ]);
        DB::table('sub_courses')->insert([
            ['id' => 1,'course_id' => 1,'name' => 'CA Intermediate','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 2,'course_id' => 2,'name' => 'Foundation Course','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 3,'course_id' => 2,'name' => 'Foundation Test','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 4,'course_id' => 3,'name' => 'Plane','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 5,'course_id' => 3,'name' => 'Plane without SP','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 6,'course_id' => 3,'name' => 'Math','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 7,'course_id' => 3,'name' => 'IT','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
            ['id' => 8,'course_id' => 3,'name' => '11th-12th Test','fee' => 0, 'gst' => 0,'created_at' => date('Y-m-d h:i:s'),'updated_at' => date('Y-m-d h:i:s')],
        ]);
    }
}
