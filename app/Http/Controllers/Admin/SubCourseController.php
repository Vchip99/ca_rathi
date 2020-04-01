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

class SubCourseController extends Controller
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
    protected $validateSubCourse = [
        'course' => 'required',
        'name' => 'required'
    ];

    /**
     * show all Sub Course
     */
    protected function show(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$subCourses = SubCourse::all();
    	return view('admin.subcourse.list', compact('subCourses'));
    }

    /**
     * show UI for create sub course
     */
    protected function create(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $courses = Course::all();
        $subCourse = new SubCourse;
    	return view('admin.subcourse.create', compact('courses','subCourse'));
    }

    /**
     *  store SUb Course
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateSubCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        DB::beginTransaction();
        try
        {
            $subCourse = SubCourse::addOrUpdateSubCourse($request);
            if(is_object($subCourse)){
                DB::commit();
                return Redirect::to('admin/manage-subcourse')->with('message', 'Sub Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while create sub course.');
        }
		return Redirect::to('admin/manage-subcourse');
    }

    /**
     * edit Sub Course
     */
    protected function edit($id){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$subCourseId = InputSanitise::inputInt(json_decode($id));
    	if(isset($subCourseId)){
    		$subCourse = SubCourse::find($subCourseId);
    		if(is_object($subCourse)){
                $courses = Course::all();
    			return view('admin.subcourse.create', compact('courses','subCourse'));
    		}
    	}
		return Redirect::to('admin/manage-subcourse');
    }

    /**
     * update Sub Course
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateSubCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        $subCourseId = InputSanitise::inputInt($request->get('subcourse_id'));
        if(isset($subCourseId)){
            DB::beginTransaction();
            try
            {
                $subCourse = SubCourse::addOrUpdateSubCourse($request, true);
                if(is_object($subCourse)){
                    DB::commit();
                    return Redirect::to('admin/manage-subcourse')->with('message', 'Sub Course updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong while update sub course.');
            }
        }
        return Redirect::to('admin/manage-subcourse');
    }

    /**
     * delete Sub Course
     */
    protected function delete(Request $request){
    	$subCourseId = InputSanitise::inputInt($request->get('subcourse_id'));
    	if(isset($subCourseId)){
    		$subCourse = SubCourse::find($subCourseId);
    		if(is_object($subCourse)){
                DB::beginTransaction();
                try
                {
                    Batch::deleteBatchesBySubCourseId($subCourse->id);
                    UserCourse::deleteUserCoursesBySubCourseId($subCourse->id);
                    CoursePayment::softDeleteBySubCourseId($subCourse->id);
        			$subCourse->delete();
                    DB::commit();
                    return Redirect::to('admin/manage-subcourse')->with('message', 'Sub Course deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong while delete sub course.');
                }
            }
        }
		return Redirect::to('admin/manage-subcourse');
    }

    protected function getSubCoursesByCourseId(Request $request){
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        return SubCourse::getSubCoursesByCourseId($courseId);
    }
}