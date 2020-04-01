<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Course;
use App\Models\SubCourse;
use App\Models\CoursePayment;
use App\Models\User;
use Validator, Session, Auth, DB,Redirect,Hash;
use App\Libraries\InputSanitise;

class SubAdminController extends Controller
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
    protected $validateAdmin = [
        'name' => 'required',
        'email' => 'required|email|max:255|unique:admins',
        'type' => 'required',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
    ];

    protected $validateUpdateAdmin = [
        'name' => 'required',
        'email' => 'required|email|max:255',
        'type' => 'required',
    ];

    /**
     * show all admins
     */
    protected function show(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$admins = Admin::whereIn('type',[Admin::Admin,Admin::SubAdmin])->get();
        $types = [Admin::Admin => 'Admin',Admin::SubAdmin => 'SubAdmin'];
    	return view('admin.subadmin.list', compact('admins', 'types'));
    }

    /**
     * show UI for create admin
     */
    protected function create(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$admin = new Admin;
        $types = [Admin::Admin => 'Admin',Admin::SubAdmin => 'SubAdmin'];
    	return view('admin.subadmin.create', compact('admin', 'types'));
    }

    /**
     *  store admin
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateAdmin);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $admin = Admin::createOrUpdateAdmin($request);
            if(is_object($admin)){
                DB::commit();
                return Redirect::to('admin/manage-admin')->with('message', 'Admin created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while create admin.');
        }
		return Redirect::to('admin/manage-admin');
    }

    /**
     * edit admin
     */
    protected function edit($id){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
    	$adminId = InputSanitise::inputInt(json_decode($id));
    	if(isset($adminId)){
    		$admin = Admin::find($adminId);
    		if(is_object($admin)){
                $types = [Admin::Admin => 'Admin',Admin::SubAdmin => 'SubAdmin'];
                return view('admin.subadmin.create', compact('admin', 'types'));
    		}
    	}
		return Redirect::to('admin/manage-admin');
    }

    /**
     * update admin
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateUpdateAdmin);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $adminId = InputSanitise::inputInt($request->get('admin_id'));
        if(isset($adminId)){
            DB::beginTransaction();
            try
            {
                $admin = Admin::createOrUpdateAdmin($request, true);
                if(is_object($admin)){
                    DB::commit();
                    return Redirect::to('admin/manage-admin')->with('message', 'Admin updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong while update admin.');
            }
        }
        return Redirect::to('admin/manage-admin');
    }

    /**
     * delete Admin
     */
    protected function delete(Request $request){
    	$adminId = InputSanitise::inputInt($request->get('admin_id'));
    	if(isset($adminId)){
    		$admin = Admin::find($adminId);
    		if(is_object($admin)){
                DB::beginTransaction();
                try
                {
                    $superAdmin = Admin::where('type', Admin::SuperAdmin)->first();
                    if(is_object($superAdmin)){
                        $coursePayments = CoursePayment::getCoursePaymentsByAdminId($admin->id);
                        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
                            foreach($coursePayments as $coursePayment){
                                $coursePayment->generated_by = $superAdmin->id;
                                $coursePayment->save();
                            }
                        }
                    }
        			$admin->delete();
                    DB::commit();
                    return Redirect::to('admin/manage-admin')->with('message', 'Admin deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manage-admin');
    }

    protected function getSubCoursesByCourseId(Request $request){
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        return SubCourse::getSubCoursesByCourseId($courseId);
    }

    protected function isSubCoursesUsed(Request $request){
        return SubCourseDetails::isSubCoursesUsed($request);
    }

    protected function getCourseReceipt(Request $request){
        $admissionType = $request->get('admission_type');
        if('new' == $admissionType){
            $userId = InputSanitise::inputString($request->get('user_id'));
            $newUser = User::where('user_id', $userId)->first();
            if(is_object($newUser)){
                $userIdExist = 'true';
            } else {
                $userIdExist = 'false';
            }
        } else {
            $userIdExist = 'false';
        }
        $result['user_id_exist'] = $userIdExist;
        if('false' == $userIdExist){
            $result['course_receipt'] = SubCourseDetails::getCourseReceipt($request);
            $result['course_payments'] = CoursePayment::getUsersPaymentsByCourseIdBySubCourseId($request);
        }
        return $result;
    }

    protected function showPassword(){
        return view('admin.subadmin.update-password');
    }

    protected function updateAdminPassword(Request $request){
        $v = Validator::make($request->all(), [
                'old_password'     => 'required',
                'new_password'     => 'required|min:5',
                'confirm_password' => 'required|same:new_password',
            ]);

        if($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $data = $request->all();
        DB::beginTransaction();
        try
        {
            $admin = Auth::guard('admin')->user();
            if(!Hash::check($data['old_password'], $admin->password)){
                return back()->withErrors(['The given password does not match the database password']);
            }else{
                $admin->password = bcrypt($data['new_password']);
                $admin->save();
                DB::commit();
                Auth::guard('admin')->logout();
                Session::flush();
                Session::regenerate();
                return Redirect::to('admin/login')->with('message', 'please login with updated password');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return Redirect::to('admin/home')->withErrors('something went wrong while updated password.');
        }
        return Redirect::to('admin/home');
    }

}
