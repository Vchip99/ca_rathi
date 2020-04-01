<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\SubCourse;
use App\Libraries\InputSanitise;
use Redirect, DB;

class Batch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id', 'sub_course_id', 'name', 'fee', 'gst', 'receipt_by', 'gstin', 'cin', 'pan'
    ];

    protected static function addOrUpdateBatch(Request $request, $isUpdate = False){
        $name = InputSanitise::inputString($request->get('name'));
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subCourseId = InputSanitise::inputInt($request->get('subcourse'));
        $batchId = InputSanitise::inputInt($request->get('batch_id'));

        $fee = InputSanitise::inputString($request->get('fee'));
        $gst = InputSanitise::inputString($request->get('gst'));
        $receiptBy = InputSanitise::inputString($request->get('receipt_by'));
        $gstin = InputSanitise::inputString($request->get('gstin'));
        $cin = InputSanitise::inputString($request->get('cin'));
        $pan = InputSanitise::inputString($request->get('pan'));

        if($isUpdate && $batchId > 0){
            $batch = static::find($batchId);
            if(!is_object($batch)){
                return 'false';
            }
        } else {
            $batch = new static;
        }

        $batch->name = $name;
        $batch->course_id = $courseId;
        $batch->sub_course_id = $subCourseId;
        $batch->fee = $fee;
        $batch->gst = $gst;
        $batch->receipt_by = $receiptBy;
        $batch->gstin = $gstin;
        $batch->cin = $cin;
        $batch->pan = $pan;
        $batch->save();
        return $batch;
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function subcourse(){
        return $this->belongsTo(SubCourse::class, 'sub_course_id');
    }

    protected static function deleteBatchesBySubCourseId($subCourseId){
        $batches = static::where('sub_course_id', $subCourseId)->get();
        if(is_object($batches) && false == $batches->isEmpty()){
            foreach($batches as $batch){
                $batch->delete();
            }
        }
        return;
    }

    protected static function deleteBatchesByCourseId($courseId){
        $batches = static::where('course_id', $courseId)->get();
        if(is_object($batches) && false == $batches->isEmpty()){
            foreach($batches as $batch){
                $batch->delete();
            }
        }
        return;
    }

    protected static function getBatchesByCourseIdBySubCourseId($courseId,$subCourseId){
        return static::where('course_id', $courseId)->where('sub_course_id', $subCourseId)->get();
    }
}
