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

class RefundController extends Controller
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
    protected $validateRefund = [
        'course' => 'required',
        'subcourse' => 'required',
        'batch' => 'required',
        'user' => 'required',
        'refund' => 'required',
        'remark' => 'required',
    ];

    /**
     * show all refund
     */
    protected function show(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        if(Admin::SuperAdmin == $loginUser->type){
            $coursePayments = CoursePayment::getAllRefunds();
        } else {
            $coursePayments = CoursePayment::getSubAdminRefundsForToday($loginUser->id);
        }
        return view('admin.refund.list', compact('coursePayments', 'loginUser'));
    }

    /**
     * show UI for create refund
     */
    protected function create(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $courses = Course::all();
        $subCourses = [];
        $batches = [];
        $users = [];
        $coursePayment = new CoursePayment;
        $totalPaid = 0;
        return view('admin.refund.create', compact('courses','subCourses', 'batches', 'coursePayment', 'users', 'totalPaid'));
    }

    /**
     *  store refund
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateRefund);
        if($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        DB::beginTransaction();
        try
        {
            $coursePayment = CoursePayment::createRefund($request);
            if(is_object($coursePayment)){
                DB::commit();
                return Redirect::to('admin/manage-refund')->with('message', 'Refund created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while create refund.');
        }
        return Redirect::to('admin/manage-refund');
    }

    /**
     * edit refund
     */
    protected function edit($id){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $coursePaymentId = InputSanitise::inputInt(json_decode($id));
        if(isset($coursePaymentId)){
            $coursePayment = CoursePayment::find($coursePaymentId);
            if(is_object($coursePayment)){
                $totalPaid = 0;
                $courses = Course::all();
                $subCourses = SubCourse::getSubCoursesByCourseId($coursePayment->course_id);
                $batches = Batch::getBatchesByCourseIdBySubCourseId($coursePayment->course_id,$coursePayment->sub_course_id);
                $users = [];
                $userPayments = CoursePayment::getUserTotalPaidByCourseIdBySubcourseIdByBatchId($coursePayment->course_id,$coursePayment->sub_course_id,$coursePayment->batch_id,$coursePayment->user_id);
                if(is_object($userPayments) && false == $userPayments->isEmpty()){
                    foreach($userPayments as $userPayment){
                        $totalPaid += $userPayment->amount;
                    }
                }
                return view('admin.refund.create', compact('courses','subCourses', 'batches', 'coursePayment', 'users', 'totalPaid'));
            }
        }
        return Redirect::to('admin/manage-refund');
    }

    /**
     * delete refund
     */
    protected function delete(Request $request){
        $coursePaymentId = InputSanitise::inputInt($request->get('course_payment_id'));
        if(isset($coursePaymentId)){
            $coursePayment = CoursePayment::where('id',$coursePaymentId)->where('course_payment_type', CoursePayment::Refund)->first();
            if(is_object($coursePayment)){
                DB::beginTransaction();
                try
                {
                    $userObj = User::where('user_id', $coursePayment->user_id)->first();
                    if(!is_object($userObj)){
                        return back()->withErrors('user is not exist for this refund.');
                    }
                    $userCourse = UserCourse::addNewUserCourse($userObj->id,$coursePayment->course_id,$coursePayment->sub_course_id,$coursePayment->batch_id);
                    if(is_object($userCourse)){
                        $coursePayment->delete();
                        DB::commit();
                        return Redirect::to('admin/manage-refund')->with('message', 'Refund deleted successfully!');
                    } else {
                        return back()->withErrors('something went wrong while create user course.');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong while delete refund.');
                }
            }
        }
        return Redirect::to('admin/manage-refund');
    }

}