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

class DiscountController extends Controller
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
    protected $validateDiscount = [
        'course' => 'required',
        'subcourse' => 'required',
        'batch' => 'required',
        'user' => 'required',
        'discount' => 'required',
        'remark' => 'required',
    ];

    /**
     * show all discount
     */
    protected function show(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $coursePayments = CoursePayment::getAllDiscounts();
        return view('admin.discount.list', compact('coursePayments', 'loginUser'));
    }

    /**
     * show UI for create discount
     */
    protected function create(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $courses = Course::all();
        $subCourses = [];
        $batches = [];
        $users = [];
        $coursePayment = new CoursePayment;
        return view('admin.discount.create', compact('courses','subCourses', 'batches', 'coursePayment', 'users'));
    }

    /**
     *  store discount
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateDiscount);
        if($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        DB::beginTransaction();
        try
        {
            $coursePayment = CoursePayment::createDiscount($request);
            if(is_object($coursePayment)){
                DB::commit();
                return Redirect::to('admin/manage-discount')->with('message', 'Discount created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while create discount.');
        }
        return Redirect::to('admin/manage-discount');
    }

    /**
     * edit discount
     */
    protected function edit($id){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $coursePaymentId = InputSanitise::inputInt(json_decode($id));
        if(isset($coursePaymentId)){
            $coursePayment = CoursePayment::find($coursePaymentId);
            if(is_object($coursePayment)){
                $courses = Course::all();
                $subCourses = SubCourse::getSubCoursesByCourseId($coursePayment->course_id);
                $batches = Batch::getBatchesByCourseIdBySubCourseId($coursePayment->course_id,$coursePayment->sub_course_id);
                $users = User::getUsersByCourseIdBySubCourseIdByBatchId($coursePayment->course_id,$coursePayment->sub_course_id,$coursePayment->batch_id);
                return view('admin.discount.create', compact('courses','subCourses', 'batches', 'coursePayment', 'users'));
            }
        }
        return Redirect::to('admin/manage-discount');
    }

    /**
     * delete discount
     */
    protected function delete(Request $request){
        $coursePaymentId = InputSanitise::inputInt($request->get('course_payment_id'));
        if(isset($coursePaymentId)){
            $coursePayment = CoursePayment::where('id',$coursePaymentId)->where('course_payment_type', CoursePayment::Discount)->first();
            if(is_object($coursePayment)){
                DB::beginTransaction();
                try
                {
                    $coursePayment->delete();
                    DB::commit();
                    return Redirect::to('admin/manage-discount')->with('message', 'Discount deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong while delete discount.');
                }
            }
        }
        return Redirect::to('admin/manage-discount');
    }

    /**
     *  get users ByCourseId BySubCourseId by batchid
     */
    protected function getUsersByCourseIdBySubCourseIdByBatchId(Request $request){
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse_id'));
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        return User::getUsersByCourseIdBySubCourseIdByBatchId($courseId,$subcourseId,$batchId);
    }
}