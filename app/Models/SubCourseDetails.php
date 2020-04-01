<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Course;
use App\Models\SubCourse;
use Redirect, DB;

class SubCourseDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id', 'sub_course_id', 'fee', 'gst', 'receipt_by', 'gstin', 'cin', 'pan'
    ];

    /**
     *  create/update course receipt
     */
    protected static function addOrUpdateCourseReceipt( Request $request, $isUpdate=false){

    	$courseId = InputSanitise::inputInt($request->get('course'));
    	$subcourseId = InputSanitise::inputInt($request->get('sub_course'));
    	$fee = InputSanitise::inputString($request->get('fee'));
    	$gst = InputSanitise::inputString($request->get('gst'));
    	$receiptBy = InputSanitise::inputString($request->get('receipt_by'));
        $gstin = InputSanitise::inputString($request->get('gstin'));
        $cin = InputSanitise::inputString($request->get('cin'));
        $pan = InputSanitise::inputString($request->get('pan'));
    	$courseReceiptId = InputSanitise::inputInt($request->get('course_receipt_id'));

        if( $isUpdate && isset($courseReceiptId)){
            $courseReceipt = static::find($courseReceiptId);
            if(!is_object($courseReceipt)){
            	return Redirect::to('admin/manage-course-receipt');
            }
        } else{
            $courseReceipt = new static;
        }
        $courseReceipt->course_id = $courseId;
		$courseReceipt->sub_course_id = $subcourseId;
		$courseReceipt->fee = $fee;
		$courseReceipt->gst = $gst;
		$courseReceipt->receipt_by = $receiptBy;
        $courseReceipt->gstin = $gstin;
        $courseReceipt->cin = $cin;
        $courseReceipt->pan = $pan;
		$courseReceipt->save();

        return $courseReceipt;
    }

    protected static function isSubCoursesUsed(Request $request){
    	$courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse'));
        $courseReceiptId = InputSanitise::inputInt($request->get('course_receipt_id'));
        $result = static::where('course_id', $courseId)->where('sub_course_id', $subcourseId);
        if(!empty($courseReceiptId)){
            $result->where('id', '!=', $courseReceiptId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

	public function subCourse(){
        return $this->belongsTo(SubCourse::class, 'sub_course_id');
    }

    protected static function getCourseReceipt(Request $request){
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse'));
        return static::where('course_id', $courseId)->where('sub_course_id', $subcourseId)->first();
    }

    protected static function getCourseReceiptByCourseIdBySubCourseId($courseId,$subcourseId){
        return static::where('course_id', $courseId)->where('sub_course_id', $subcourseId)->first();
    }
}
