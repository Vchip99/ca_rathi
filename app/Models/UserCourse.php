<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;

class UserCourse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'course_id', 'sub_course_id', 'batch_id'
    ];

    /**
     * add new course of user
     */
    protected static function addNewUserCourse($userId,$courseId,$subcourseId,$batchId){
        $newUser = static::where('user_id', $userId)->where('course_id', $courseId)->where('sub_course_id', $subcourseId)->where('batch_id', $batchId)->first();
        if(!is_object($newUser)){
            return static::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'sub_course_id' => $subcourseId,
                'batch_id' => $batchId,
            ]);
        }
        return;
    }

    protected static function getUsersBySubCourseId(Request $request){
        $subCourseId = $request->get('sub_course_id');
        if('All' == $subCourseId){
            return static::join('users', 'users.id', '=', 'user_courses.user_id')->select('users.*')->groupBy('user_courses.user_id')->get();
        } else {
            return static::join('users', 'users.id', '=', 'user_courses.user_id')->where('user_courses.sub_course_id', $subCourseId)->select('users.*')->groupBy('user_courses.user_id')->get();
        }
    }

    protected static function deleteUserCoursesByCourseId($courseId){
        $userCourses = static::where('course_id', $courseId)->get();
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                $userCourse->delete();
            }
        }
        return;
    }

    protected static function deleteUserCoursesBySubCourseId($subCourseId){
        $userCourses = static::where('sub_course_id', $subCourseId)->get();
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                $userCourse->delete();
            }
        }
        return;
    }

    protected static function deleteUserCoursesByBatchId($batchId){
        $userCourses = static::where('batch_id', $batchId)->get();
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                $userCourse->delete();
            }
        }
        return;
    }

    /**
     * delete user course by user id course id sub course id batch id
     */
    protected static function deleteUserCourseByCourseIdBySubCourseIdByBatchId($userId,$courseId,$subcourseId,$batchId){
        $userCourse = static::where('user_id', $userId)->where('course_id', $courseId)->where('sub_course_id', $subcourseId)->where('batch_id', $batchId)->first();
        if(is_object($userCourse)){
            $userCourse->delete();
        }
        return;
    }
}
