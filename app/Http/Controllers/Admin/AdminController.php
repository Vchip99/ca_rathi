<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudentEnquiry;
use App\Models\Admin;
use DB, Redirect,Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('admin.home');
    }

    protected function enquiries(){
        $adminUser = Auth::guard('admin')->user();
        if(4 == $adminUser->type){
            $enquiries = StudentEnquiry::whereDate('created_at',date('Y-m-d'))->where('enquiry_by', $adminUser->name)->orderBy('id', 'desc')->get();
        } else if(2 == $adminUser->type){
            $enquiries = StudentEnquiry::orderBy('id', 'desc')->get();
        } else {
            return Redirect::to('admin/home');
        }
        return view('admin.enquiry.list', compact('enquiries','adminUser'));
    }

    protected function createEnquiry(){
        $enquiry = new StudentEnquiry;
        return view('admin.enquiry.create', compact('enquiry'));
    }

    protected function storeEnquiry(Request $request){
        DB::beginTransaction();
        try
        {
            $enquiry = StudentEnquiry::addEnquiry($request);
            if(is_object($enquiry)){
                DB::commit();
                return Redirect::to('admin/enquiries')->with('message', 'Enquiry created successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/enquiries');
    }

    protected function editEnquiry($id){
        $id = json_decode($id);
        $enquiry = StudentEnquiry::find($id);
        if(is_object($enquiry)){
            return view('admin.enquiry.create', compact('enquiry'));
        }
        return Redirect::to('admin/enquiries');
    }

    protected function deleteEnquiry(Request $request){
        $id = json_decode($request->get('enquiry_id'));
        DB::beginTransaction();
        try
        {
            $enquiry = StudentEnquiry::find($id);
            if(is_object($enquiry)){
                $enquiry->delete();
                DB::commit();
                return Redirect::to('admin/enquiries')->with('message','Enquiry record deleted successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/enquiries');
    }

    protected function getEnquiryByCourse(Request $request){
        return StudentEnquiry::getEnquiryByCourse($request);
    }
}
