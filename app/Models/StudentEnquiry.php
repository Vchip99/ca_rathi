<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use Redirect, DB;

class StudentEnquiry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'course', 'other', 'ssc', 'hsc', 'graduation','address','city', 'student_no', 'parent_no', 'land_line_no', 'reference_by', 'enquiry_by','student_interest',
    ];

    /**
     * add enquiry
     */
    protected static function addEnquiry(Request $request){
        $name = InputSanitise::inputString($request->get('name'));
        if(count($request->get('course_name')) > 0){
        	foreach($request->get('course_name') as $index => $courseName)
        		if(0 == $index){
        			$course = $courseName;
        		} else {
        			$course .= ','.$courseName;
        		}
        } else {
        	$course = '';
        }
        $other = InputSanitise::inputString($request->get('other'));

        $sscMedium = InputSanitise::inputString($request->get('ssc_medium'));
        $sscStream = InputSanitise::inputString($request->get('ssc_stream'));
        $sscSchool = str_replace('|', ' ', InputSanitise::inputString($request->get('ssc_school')));

        $hscMedium = InputSanitise::inputString($request->get('hsc_medium'));
        $hscStream = InputSanitise::inputString($request->get('hsc_stream'));
        $hscSchool = str_replace('|', ' ', InputSanitise::inputString($request->get('hsc_school')));

        $graduationMedium = InputSanitise::inputString($request->get('graduation_medium'));
        $graduationStream = InputSanitise::inputString($request->get('graduation_stream'));
        $graduationSchool = str_replace('|', ' ', InputSanitise::inputString($request->get('graduation_school')));

        $address = InputSanitise::inputString($request->get('address'));
        $city = InputSanitise::inputString($request->get('city'));
        $studentNo = InputSanitise::inputString($request->get('student_no'));
        $parentNo = InputSanitise::inputString($request->get('parent_no'));
        $landLineNo = InputSanitise::inputString($request->get('land_line_no'));
        $referenceBy = InputSanitise::inputString($request->get('reference_by'));
        $enquiryBy = InputSanitise::inputString($request->get('enquiry_by'));
        $studentInterest = InputSanitise::inputString($request->get('student_interest'));

        return static::create([
            'name' => $name,
            'course' => $course,
            'other' => $other,
            'ssc' => $sscMedium.'|'.$sscStream.'|'.$sscSchool,
            'hsc' => $hscMedium.'|'.$hscStream.'|'.$hscSchool,
            'graduation' => $graduationMedium.'|'.$graduationStream.'|'.$graduationSchool,
            'city' => $city,
            'address' => $address,
            'student_no' => $studentNo,
            'parent_no' => $parentNo,
            'land_line_no' => $landLineNo,
            'reference_by' => $referenceBy,
            'enquiry_by' => $enquiryBy,
            'student_interest' => ($studentInterest)?:0,
        ]);

    }

    protected static function getEnquiryByCourse(Request $request){
    	$fromDate = $request->get('from_date');
    	$toDate = $request->get('to_date');
    	$course = $request->get('course');

    	$result = static::whereRaw("find_in_set('$course' , course)");
	 	if(is_string($fromDate)){
            $result->where('created_at','>=', $fromDate." 00:00:00");
        }
        if(is_string($toDate)){
            $result->where('created_at','<=', $toDate." 23:59:59");
        }
        return $result->select('id','name','course','student_no')->orderBy('id', 'desc')->get();
    }
}
