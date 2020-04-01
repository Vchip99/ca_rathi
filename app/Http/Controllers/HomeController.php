<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentEnquiry;
use DB,Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.home');
    }

    protected function enquiry(Request $request){
        DB::beginTransaction();
        try
        {
            $enquiry = StudentEnquiry::addEnquiry($request);
            if(is_object($enquiry)){
                DB::commit();
                return Redirect::to('/')->with('message', 'Thanks for enquiry. we will call you asap.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('/');
    }
}
