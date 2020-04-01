<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SubCourse;
use App\Models\Batch;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\CoursePayment;
use App\Models\Admin;
use Validator, Session, Auth, DB,Redirect;
use App\Libraries\InputSanitise;

class BatchController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
    public function __construct() {
        $this->middleware('admin');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateBatch = [
        'course' => 'required',
        'subcourse' => 'required',
        'name' => 'required'
    ];

    /**
     * show all batches
     */
    protected function show(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$batches = Batch::all();
    	return view('admin.batch.list', compact('batches'));
    }

    /**
     * show UI for create batch
     */
    protected function create(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $courses = Course::all();
        $subCourses = [];
        $batch = new Batch;
    	return view('admin.batch.create', compact('courses','subCourses', 'batch'));
    }

    /**
     *  store batch
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateBatch);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        DB::beginTransaction();
        try
        {
            $batch = Batch::addOrUpdateBatch($request);
            if(is_object($batch)){
                DB::commit();
                return Redirect::to('admin/manage-batch')->with('message', 'Batch created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while create batch.');
        }
		return Redirect::to('admin/manage-batch');
    }

    /**
     * edit batch
     */
    protected function edit($id){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$batchId = InputSanitise::inputInt(json_decode($id));
    	if(isset($batchId)){
    		$batch = Batch::find($batchId);
    		if(is_object($batch)){
                $courses = Course::all();
                $subCourses = SubCourse::getSubCoursesByCourseId($batch->course_id);
                return view('admin.batch.create', compact('courses','subCourses', 'batch'));
    		}
    	}
		return Redirect::to('admin/manage-batch');
    }

    /**
     * update batch
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateBatch);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        if(isset($batchId)){
            DB::beginTransaction();
            try
            {
                $batch = Batch::addOrUpdateBatch($request, true);
                if(is_object($batch)){
                    DB::commit();
                    return Redirect::to('admin/manage-batch')->with('message', 'Batch updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong while update batch.');
            }
        }
        return Redirect::to('admin/manage-batch');
    }

    /**
     * delete batch
     */
    protected function delete(Request $request){
    	$batchId = InputSanitise::inputInt($request->get('batch_id'));
    	if(isset($batchId)){
    		$batch = Batch::find($batchId);
    		if(is_object($batch)){
                DB::beginTransaction();
                try
                {
                    UserCourse::deleteUserCoursesByBatchId($batch->id);
                    CoursePayment::softDeleteByBatchId($batch->id);
        			$batch->delete();
                    DB::commit();
                    return Redirect::to('admin/manage-batch')->with('message', 'Batch deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong while delete batch.');
                }
            }
        }
		return Redirect::to('admin/manage-batch');
    }

    /**
     *  get Batches ByCourseId BySubCourseId
     */
    protected function getBatchesByCourseIdBySubCourseId(Request $request){
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $subCourseId = InputSanitise::inputInt($request->get('subcourse_id'));
        return Batch::getBatchesByCourseIdBySubCourseId($courseId,$subCourseId);
    }
}