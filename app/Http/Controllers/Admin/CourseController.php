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

class CourseController extends Controller
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
    protected $validateCourse = [
        'name' => 'required'
    ];

    /**
     * show all Course
     */
    protected function show(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$courses = Course::all();
    	return view('admin.course.list', compact('courses'));
    }

    /**
     * show UI for create Course
     */
    protected function create(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $course = new Course;
    	return view('admin.course.create', compact('course'));
    }

    /**
     *  store Course
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        DB::beginTransaction();
        try
        {
            $course = Course::addOrUpdateCourse($request);
            if(is_object($course)){
                DB::commit();
                return Redirect::to('admin/manage-course')->with('message', 'Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while creating course.');
        }
		return Redirect::to('admin/manage-course');
    }

    /**
     * edit Course
     */
    protected function edit($id){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$courseId = InputSanitise::inputInt(json_decode($id));
    	if(isset($courseId)){
    		$course = Course::find($courseId);
    		if(is_object($course)){
    			return view('admin.course.create', compact('course'));
    		}
    	}
		return Redirect::to('admin/manage-course');
    }

    /**
     * update Course
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        if(isset($courseId)){
            DB::beginTransaction();
            try
            {
                $course = Course::addOrUpdateCourse($request, true);
                if(is_object($course)){
                    DB::commit();
                    return Redirect::to('admin/manage-course')->with('message', 'Course updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong while updating course.');
            }
        }
        return Redirect::to('admin/manage-course');
    }

    /**
     * delete Course
     */
    protected function delete(Request $request){
    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
    		$course = Course::find($courseId);
    		if(is_object($course)){
                DB::beginTransaction();
                try
                {
                    SubCourse::deleteSubCoursesByCourseId($course->id);
                    Batch::deleteBatchesByCourseId($course->id);
                    UserCourse::deleteUserCoursesByCourseId($course->id);
                    CoursePayment::softDeleteByCourseId($course->id);
        			$course->delete();
                    DB::commit();
                    return Redirect::to('admin/manage-course')->with('message', 'Course deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong while deleteing course.');
                }
            }
        }
		return Redirect::to('admin/manage-course');
    }
}