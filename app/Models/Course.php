<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use Redirect, DB;

class Course extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    protected static function addOrUpdateCourse(Request $request, $isUpdate = False){
    	$name = InputSanitise::inputString($request->get('name'));
    	$courseId = InputSanitise::inputInt($request->get('course_id'));

    	if($isUpdate && $courseId > 0){
    		$course = static::find($courseId);
    		if(!is_object($course)){
    			return 'false';
    		}
    	} else {
    		$course = new static;
    	}

    	$course->name = $name;
    	$course->save();
    	return $course;
    }
}
