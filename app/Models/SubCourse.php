<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Libraries\InputSanitise;
use Redirect, DB;

class SubCourse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id', 'name'
    ];

    protected static function addOrUpdateSubCourse(Request $request, $isUpdate = False){
        $name = InputSanitise::inputString($request->get('name'));
        $courseId = InputSanitise::inputInt($request->get('course'));

        $subCourseId = InputSanitise::inputInt($request->get('subcourse_id'));

        if($isUpdate && $subCourseId > 0){
            $subCourse = static::find($subCourseId);
            if(!is_object($subCourse)){
                return 'false';
            }
        } else {
            $subCourse = new static;
        }

        $subCourse->name = $name;
        $subCourse->course_id = $courseId;
        $subCourse->save();
        return $subCourse;
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

    protected static function getSubCoursesByCourseId($courseId ){
    	if($courseId > 0){
    		return static::where('course_id', $courseId)->get();
    	}
    	return;
    }

    protected static function deleteSubCoursesByCourseId($courseId){
        $subCourses = static::where('course_id', $courseId)->get();
        if(is_object($subCourses) && false == $subCourses->isEmpty()){
            foreach($subCourses as $subCours){
                $subCours->delete();
            }
        }
        return;
    }
}
