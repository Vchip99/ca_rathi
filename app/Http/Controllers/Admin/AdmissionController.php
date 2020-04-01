<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CoursePayment;
use App\Models\Course;
use App\Models\SubCourse;
use App\Models\Batch;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\Admin;
use Validator, Session, Auth, DB,Redirect, Input;
use App\Libraries\InputSanitise;
use Excel;
use PDF;

class AdmissionController extends Controller
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
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCoursePayment = [
        'f_name' => 'required',
        'm_name' => 'required',
        'l_name' => 'required',
        'user_id' => 'required',
        'phone' => 'required|regex:/[0-9]{10}/',
        'course' => 'required',
        'sub_course' => 'required',
        'batch' => 'required',
        'fee' => 'required',
        'payment_type' => 'required',
        'amount' => 'required',
    ];

    protected $receiptByArr = [
        'ram_rathi' => 'Ram Rathi',
        'shyam_rathi' => 'Shyam Rathi',
        'giridhar_rathi' => 'Giridhar Rathi',
        'dipti_rathi' => 'Dipti Rathi',
        'sunita_rathi' => 'Sunita Rathi',
    ];

    protected $validateAdmission = [
        'f_name' => 'required',
        'm_name' => 'required',
        'l_name' => 'required',
        'user_id' => 'required',
        'phone' => 'required|regex:/[0-9]{10}/',
    ];

    // /**
    //  * Show the application form.
    //  *
    //  */
    // public function createAdmission()
    // {
    //     return view('admin.admission-form.admission');
    // }

    // /**
    //  * Store the application form.
    //  *
    //  */
    // public function storeAdmission(Request $request)
    // {
    //     $v = Validator::make($request->all(), $this->validateAdmission);
    //     if ($v->fails())
    //     {
    //         return redirect()->back()->withErrors($v->errors());
    //     }
    //     DB::beginTransaction();
    //     try
    //     {
    //         $result = User::addNewAdmission($request);
    //         if('true' == $result){
    //             DB::commit();
    //             return Redirect::to('admin/create-student-admission')->with('message', 'Admission created successfully!');
    //         } else {
    //             return Redirect::to('admin/create-student-admission')->withErrors('User id already exists. please user another user id.')->withInput();
    //         }
    //     }
    //     catch(\Exception $e)
    //     {
    //         DB::rollback();
    //         return redirect()->back()->withErrors('something went wrong.');
    //     }
    //     return Redirect::to('admin/create-student-admission');
    // }


    /**
     * Show the student form.
     */
    public function create(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $lastUser = User::orderBy('id', 'desc')->first();
        if(is_object($lastUser)){
            $nextUserId = (int)$lastUser->id + 1;
        } else {
            $nextUserId = 1;
        }
        $courses = Course::all();
        return view('admin.admission-form.create', compact('courses', 'nextUserId'));
    }

    /**
     * Store the student form.
     */
    public function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCoursePayment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $fee = InputSanitise::inputInt($request->get('fee'));
        $gst = InputSanitise::inputInt($request->get('gst'));
        $feeType = InputSanitise::inputString($request->get('fee_type'));
        $paymentType = InputSanitise::inputInt($request->get('payment_type'));
        $amount = InputSanitise::inputInt($request->get('amount'));
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('sub_course'));
        $batchId = InputSanitise::inputInt($request->get('batch'));
        $paidAmount = InputSanitise::inputInt($request->get('paid_amount'));
        $ramRathi = InputSanitise::inputString($request->get('ram_rathi'));
        $shyamRathi = InputSanitise::inputString($request->get('shyam_rathi'));
        $giridharRathi = InputSanitise::inputString($request->get('giridhar_rathi'));
        $diptiRathi = InputSanitise::inputString($request->get('dipti_rathi'));
        $sunitaRathi = InputSanitise::inputString($request->get('sunita_rathi'));
        $caIntermediateTotal = 0;
        if(0 < $amount){
            if(0 == $paymentType){
                // if(0 >= $amount){
                //     return redirect()->back()->withErrors('please enter amount greter than 0.')->withInput();
                // }
                if('on' == $feeType){
                    if(($paidAmount + $amount) > ($fee + $gst)){
                        return redirect()->back()->withErrors('entered amount greter than total amount .')->withInput();
                    }
                } else {
                    if(($paidAmount + $amount) > $fee){
                        return redirect()->back()->withErrors('entered amount greter than total amount .')->withInput();
                    }
                }
            }
            if(empty($gst)){
                if(0 == $paymentType && $amount >= $fee){
                    return redirect()->back()->withErrors('please enter amount less than fee.')->withInput();
                }
            } else {
                if(0 == $paymentType){
                    if('on' == $feeType){
                        if($amount >= ($fee + $gst)){
                            return redirect()->back()->withErrors('please enter amount less than fee + gst.')->withInput();
                        }
                    } else {
                        if($amount >= $fee){
                            return redirect()->back()->withErrors('please enter amount less than fee.')->withInput();
                        }
                    }
                }
            }
            if(1 == $courseId && 1 == $subcourseId){
                if($ramRathi > 0){
                    $caIntermediateTotal += $ramRathi;
                }
                if($shyamRathi > 0){
                    $caIntermediateTotal += $shyamRathi;
                }
                if($giridharRathi > 0){
                    $caIntermediateTotal += $giridharRathi;
                }
                if($diptiRathi > 0){
                    $caIntermediateTotal += $diptiRathi;
                }
                if($sunitaRathi > 0){
                    $caIntermediateTotal += $sunitaRathi;
                }
                if($caIntermediateTotal > 0 && $caIntermediateTotal > $amount){
                    return redirect()->back()->withErrors('receipt by amount greter than total amount.')->withInput();
                }
                if($caIntermediateTotal > 0 && $caIntermediateTotal < $amount){
                    return redirect()->back()->withErrors('receipt by amount less than total amount.')->withInput();
                }
            }
        }
        if(1 == $courseId && 1 == $subcourseId){
            if(empty($ramRathi) && empty($shyamRathi) && empty($giridharRathi) && empty($diptiRathi) && empty($sunitaRathi) && $amount > 0){
                return redirect()->back()->withErrors('please enter amount to receipt by.');
            }
        }
        DB::beginTransaction();
        try
        {
            $courseReceipt = CoursePayment::addAdmissionPayment($request);
            if(is_object($courseReceipt)){
                CoursePayment::UpdateRemainderDateByCourseIdBySubCourseIdByBatchIdByUserId($courseReceipt->course_id,$courseReceipt->sub_course_id,$courseReceipt->batch_id,$courseReceipt->user_id,$courseReceipt->id,$courseReceipt->remainder_date);
                $user = User::addNewUser($request);
                if(is_object($user)){
                    UserCourse::addNewUserCourse($user->id,$courseReceipt->course_id,$courseReceipt->sub_course_id,$courseReceipt->batch_id);
                }
                DB::commit();
                 // Account details
                $apiKey = urlencode(config('custom.sms_key'));
                // Message details
                $mobileNo = '91'.$user->phone;
                $numbers = array($mobileNo);
                $sender = urlencode('TXTLCL');
                $userMessage = 'Dear '.$user->f_name.' '.$user->l_name.', Thanks for payment. Your UserId-'.$user->user_id.' -- RCF';
                $message = rawurlencode($userMessage);

                $numbers = implode(',', $numbers);

                // Prepare data for POST request
                $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

                // Send the POST request with cURL
                $ch = curl_init('https://api.textlocal.in/send/');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                return Redirect::to('admin/manage-course-payment')->with('message', 'Course payment done successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while create admission.');
        }
        return Redirect::to('admin/create-admission-receipt');
    }

    protected function getUsersBySubCourseId(Request $request){
        return UserCourse::getUsersBySubCourseId($request);
    }

    protected function showReceipt($id){
        $courseReceiptArr = [] ;
        $courseReceiptTaxArr = [];
        $coursePayment = CoursePayment::find(json_decode($id));
        if(!is_object($coursePayment)){
            return Redirect::to('admin/home');
        }
        $batch = Batch::find($coursePayment->batch_id);
        if(!is_object($batch)){
            return Redirect::to('admin/manage-course-payment')->withErrors('sub course is not available.');
        }
        PDF::AddPage();
        if( 1 == $coursePayment->course_id && 1 == $coursePayment->sub_course_id){
            $receiptIndex = 0;
            $receiptArr = explode(',', $coursePayment->receipt_id);
            if($coursePayment->ram_rathi > 0){
                $courseReceiptArr = [
                    'receipt_id' => $receiptArr[$receiptIndex],
                    'f_name' => $coursePayment->f_name,
                    'm_name' => $coursePayment->m_name,
                    'l_name' => $coursePayment->l_name,
                    'user_id' => $coursePayment->user_id,
                    'course' => $coursePayment->course->name,
                    'subcourse' => $coursePayment->subcourse->name,
                    'amount' => $coursePayment->ram_rathi,
                    'fee_type' => $coursePayment->fee_type,
                    'receipt_by' => $this->receiptByArr['ram_rathi'],
                    'date' => date('d-m-Y',strtotime($coursePayment->payment_date)),
                    'gstin' => $batch->gstin,
                    'cin' =>  $batch->cin,
                    'pan' =>  $batch->pan,
                    'gst' =>  $batch->gst,
                ];

                $receiptIndex++;

                // set default form properties
                PDF::setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
                $tbl = $this->createPdfHtml($courseReceiptArr);
                PDF::writeHTML($tbl, false, false, false, false, '');
            }

            if($coursePayment->shyam_rathi > 0){
                $courseReceiptArr = [
                    'receipt_id' => $receiptArr[$receiptIndex],
                    'f_name' => $coursePayment->f_name,
                    'm_name' => $coursePayment->m_name,
                    'l_name' => $coursePayment->l_name,
                    'user_id' => $coursePayment->user_id,
                    'course' => $coursePayment->course->name,
                    'subcourse' => $coursePayment->subcourse->name,
                    'amount' => $coursePayment->shyam_rathi,
                    'fee_type' => $coursePayment->fee_type,
                    'receipt_by' => $this->receiptByArr['shyam_rathi'],
                    'date' => date('d-m-Y',strtotime($coursePayment->payment_date)),
                    'gstin' => $batch->gstin,
                    'cin' =>  $batch->cin,
                    'pan' =>  $batch->pan,
                    'gst' =>  $batch->gst,
                ];
                $receiptIndex++;
                // set default form properties
                PDF::setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
                $tbl = $this->createPdfHtml($courseReceiptArr);

                PDF::writeHTML($tbl, false, false, false, false, '');
            }

            if($coursePayment->giridhar_rathi > 0){
                $courseReceiptArr = [
                    'receipt_id' => $receiptArr[$receiptIndex],
                    'f_name' => $coursePayment->f_name,
                    'm_name' => $coursePayment->m_name,
                    'l_name' => $coursePayment->l_name,
                    'user_id' => $coursePayment->user_id,
                    'course' => $coursePayment->course->name,
                    'subcourse' => $coursePayment->subcourse->name,
                    'amount' => $coursePayment->giridhar_rathi,
                    'fee_type' => $coursePayment->fee_type,
                    'receipt_by' => $this->receiptByArr['giridhar_rathi'],
                    'date' => date('d-m-Y',strtotime($coursePayment->payment_date)),
                    'gstin' => $batch->gstin,
                    'cin' =>  $batch->cin,
                    'pan' =>  $batch->pan,
                    'gst' =>  $batch->gst,
                ];
                $receiptIndex++;
                // set default form properties
                PDF::setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
                $tbl = $this->createPdfHtml($courseReceiptArr);

                PDF::writeHTML($tbl, false, false, false, false, '');
            }

            if($coursePayment->dipti_rathi > 0){
                $courseReceiptArr = [
                    'receipt_id' => $receiptArr[$receiptIndex],
                    'f_name' => $coursePayment->f_name,
                    'm_name' => $coursePayment->m_name,
                    'l_name' => $coursePayment->l_name,
                    'user_id' => $coursePayment->user_id,
                    'course' => $coursePayment->course->name,
                    'subcourse' => $coursePayment->subcourse->name,
                    'amount' => $coursePayment->dipti_rathi,
                    'fee_type' => $coursePayment->fee_type,
                    'receipt_by' => $this->receiptByArr['dipti_rathi'],
                    'date' => date('d-m-Y',strtotime($coursePayment->payment_date)),
                    'gstin' => $batch->gstin,
                    'cin' =>  $batch->cin,
                    'pan' =>  $batch->pan,
                    'gst' =>  $batch->gst,
                ];
                $receiptIndex++;
                // set default form properties
                PDF::setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
                $tbl = $this->createPdfHtml($courseReceiptArr);

                PDF::writeHTML($tbl, false, false, false, false, '');
            }

            if($coursePayment->sunita_rathi > 0){
                $courseReceiptArr = [
                    'receipt_id' => $receiptArr[$receiptIndex],
                    'f_name' => $coursePayment->f_name,
                    'm_name' => $coursePayment->m_name,
                    'l_name' => $coursePayment->l_name,
                    'user_id' => $coursePayment->user_id,
                    'course' => $coursePayment->course->name,
                    'subcourse' => $coursePayment->subcourse->name,
                    'amount' => $coursePayment->sunita_rathi,
                    'fee_type' => $coursePayment->fee_type,
                    'receipt_by' => $this->receiptByArr['sunita_rathi'],
                    'date' => date('d-m-Y',strtotime($coursePayment->payment_date)),
                    'gstin' => $batch->gstin,
                    'cin' =>  $batch->cin,
                    'pan' =>  $batch->pan,
                    'gst' =>  $batch->gst,
                ];
                $receiptIndex++;
                // set default form properties
                PDF::setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
                $tbl = $this->createPdfHtml($courseReceiptArr);

                PDF::writeHTML($tbl, false, false, false, false, '');
            }
        } else {
            $courseReceiptArr = [
                'receipt_id' => $coursePayment->receipt_id,
                'f_name' => $coursePayment->f_name,
                'm_name' => $coursePayment->m_name,
                'l_name' => $coursePayment->l_name,
                'user_id' => $coursePayment->user_id,
                'course' => $coursePayment->course->name,
                'subcourse' => $coursePayment->subcourse->name,
                'amount' => $coursePayment->amount,
                'fee_type' => $coursePayment->fee_type,
                'receipt_by' => $batch->receipt_by,
                'date' => date('d-m-Y',strtotime($coursePayment->payment_date)),
                'gstin' => $batch->gstin,
                'cin' =>  $batch->cin,
                'pan' =>  $batch->pan,
                'gst' =>  $batch->gst,
            ];

            // set default form properties
            PDF::setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
            $tbl = $this->createPdfHtml($courseReceiptArr);
            PDF::writeHTML($tbl, true, false, false, false, '');
        }
        PDF::Output("receipt-".$coursePayment->id.".pdf", "I");
    }

    protected function createPdfHtml($courseReceiptArr){
        $numberFormatter = new \NumberFormatter( locale_get_default(), \NumberFormatter::SPELLOUT );
        $amountInWords = $numberFormatter->format($courseReceiptArr['amount']);
        if($courseReceiptArr['gst'] > 0){
            $tbl = '<br><br><br><table border="1" cellpadding="0" cellspacing="0">';
        } else {
            $tbl = '<br><br><br><br><br><br><br><br><table border="1" cellpadding="0" cellspacing="0">';
        }
        $tbl .= '<thead>
                <tr>
                    <td colspan="10" align="center"><b>Tax Invoice</b></td>
                </tr>
            </thead>
            <tr>
                <td colspan="5" align="center"><h3>&nbsp;<u>'.$courseReceiptArr['receipt_by'].'</u></h3><br/>&nbsp;S.NO. 132A, CTS- 263/5 HANSMANI BEHIND PARVATI PETROL PUMP, PUNE</td>
                <td colspan="5" align="center">Receipt No: '.$courseReceiptArr['receipt_id'].'<br/>Date: '.$courseReceiptArr['date'].'</td>
            </tr>';
        if($courseReceiptArr['gst'] > 0){
            $tbl .= '
                    <tr>
                        <td colspan="3" align="center">GSTIN: '.$courseReceiptArr['gstin'].'</td>
                        <td colspan="3" align="center">CIN: '.$courseReceiptArr['cin'].'</td>
                        <td colspan="4" align="center">PAN: '.$courseReceiptArr['pan'].'</td>
                    </tr>';
        }
        $tbl .= '
            <tr>
                <td colspan="5" align="left">&nbsp;&nbsp;Billed To: '.$courseReceiptArr['f_name'].' '.$courseReceiptArr['m_name'].' '.$courseReceiptArr['l_name'].'<br> &nbsp;User Id: '.$courseReceiptArr['user_id'].'<br> &nbsp;State Code: 27(Maharashtra)</td>
                <td colspan="5" align="left">&nbsp;&nbsp;Shipped To: '.$courseReceiptArr['f_name'].' '.$courseReceiptArr['m_name'].' '.$courseReceiptArr['l_name'].'<br> &nbsp;User Id: '.$courseReceiptArr['user_id'].'<br> &nbsp;State Code: 27(Maharashtra)</td>
            </tr>
            <tr>
                <td colspan="10" align="left">&nbsp;&nbsp;Remarks:<br></td>
            </tr>';
        if($courseReceiptArr['gst'] > 0){
                $tbl .= '<tr>
                    <td colspan="1" align="center">Sr.No</td>
                    <td colspan="2" align="center">Service Supplied</td>
                    <td colspan="1" align="center">HSN/<br>SAC</td>
                    <td colspan="1" align="center">Qty</td>
                    <td colspan="1" align="center">Unit</td>
                    <td colspan="1" align="center">Rate Per Item</td>
                    <td colspan="2" align="center">Taxable Value (Rs.)</td>
                    <td colspan="2" align="center">'.round($courseReceiptArr['amount']/1.18,2).'</td>
                </tr>
                <tr>
                    <td rowspan="3" align="center">1</td>
                    <td rowspan="3" colspan="2" align="center">Coaching for '.$courseReceiptArr['subcourse'].'</td>
                    <td rowspan="3" colspan="1" align="center">999293</td>
                    <td rowspan="3" colspan="1" align="center">NA</td>
                    <td rowspan="3" colspan="1" align="center">NA</td>
                    <td rowspan="3" colspan="1" align="center">'.round($courseReceiptArr['amount']/1.18,2).'</td>
                    <td colspan="2" align="center">Add:CGST @9% (Rs.)</td>
                    <td colspan="2" align="center">'.round(($courseReceiptArr['amount']/1.18) * 0.09,2).'</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">Add:SGST @9% (Rs.)</td>
                    <td colspan="2" align="center">'.round(($courseReceiptArr['amount']/1.18) * 0.09,2).'</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">Total(Rs.)</td>
                    <td colspan="2" align="center">'.$courseReceiptArr['amount'].'</td>
                </tr>';
        } else {
            $tbl .= '<tr>
                <td colspan="1" align="center">Sr.No</td>
                <td colspan="5" align="center">Service Supplied</td>
                <td colspan="1" align="center">Qty</td>
                <td colspan="1" align="center">Unit</td>
                <td colspan="2" align="center">Total</td>
            </tr>
            <tr>
                <td align="center">1</td>
                <td colspan="5" align="center">Coaching for '.$courseReceiptArr['subcourse'].'</td>
                <td colspan="1" align="center">NA</td>
                <td colspan="1" align="center">NA</td>
                <td colspan="2" align="center">'.$courseReceiptArr['amount'].'</td>
            </tr>';
        }
        $tbl .= '<tr>
                <td colspan="10" align="left">&nbsp;&nbsp;Ruppes: '.$amountInWords.' only </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="left"><br/><br/><br/>&nbsp;&nbsp;Customer Signature</td>
                        <td colspan="5" align="right"><br/><br/><br/>Authorised Signature &nbsp;&nbsp;</td>
                    </tr>';
        if($courseReceiptArr['gst'] > 0){
            $tbl .= '
                </table><br><br>';
        } else {
            $tbl .= '
                </table><br><br><br>';
        }
        return $tbl;
    }

    protected function downloadCoursePayments(Request $request){

        $fromDate = $request->get('from');
        $toDate = $request->get('to');
        $resultArray[] = ['Sr. No.','UserId','Course','Sub Course','Batch','Admission','Refund','CGST','SGST','Total','Date'];
        $coursePayments = CoursePayment::downloadCoursePayments($request);
        $admissionTotal = 0;
        $refundTotal = 0;
        $cgst = 0;
        $sgst = 0;
        $userIds = [];
        $userNames = [];
        if( false == $coursePayments->isEmpty()){
            foreach($coursePayments as $index => $coursePayment){
                $userIds[] = $coursePayment->user_id;
            }
        }
        $allUsers = User::find(array_unique($userIds));
        if(is_object($allUsers) && false == $allUsers->isEmpty()){
            foreach($allUsers as $user){
                $userNames[$user->id] = $user->f_name.' '.$user->l_name;
            }
        }
        if( false == $coursePayments->isEmpty()){
            foreach($coursePayments as $index => $coursePayment){
                $result = [];
                $result['Sr. No.'] = $index +1;
                $result['UserId'] = $coursePayment->user_id.'-'.$userNames[$coursePayment->user_id];
                $result['Course'] = $coursePayment->course->name;
                $result['Sub Course'] = $coursePayment->subcourse->name;
                $result['Batch'] = $coursePayment->batch->name;
                if(1 == $coursePayment->fee_type){
                    if(1 == $coursePayment->course_payment_type){
                        $result['Admission'] = round($coursePayment->amount/1.18,2);
                        $result['Refund'] = round(0,2);
                        $result['CGST'] = round(($coursePayment->amount/1.18) * 0.09,2);
                        $result['SGST'] = round(($coursePayment->amount/1.18) * 0.09,2);

                        $admissionTotal = $admissionTotal + round($coursePayment->amount/1.18,2);
                        $cgst = $cgst + round(($coursePayment->amount/1.18) * 0.09,2);
                        $sgst = $sgst + round(($coursePayment->amount/1.18) * 0.09,2);
                    } else {
                        $result['Admission'] = round(0,2);
                        $result['Refund'] = round($coursePayment->amount/1.18,2);
                        $result['CGST'] = round(($coursePayment->amount/1.18) * 0.09,2);
                        $result['SGST'] = round(($coursePayment->amount/1.18) * 0.09,2);

                        $refundTotal = $refundTotal + round($coursePayment->amount/1.18,2);
                        $cgst = $cgst - round(($coursePayment->amount/1.18) * 0.09,2);
                        $sgst = $sgst - round(($coursePayment->amount/1.18) * 0.09,2);
                    }
                } else {
                    if(1 == $coursePayment->course_payment_type){
                        $result['Admission'] = round($coursePayment->amount,2);
                        $result['Refund'] = round(0,2);
                        $admissionTotal = $admissionTotal + $coursePayment->amount;
                        $cgst = $cgst + 0;
                        $sgst = $sgst + 0;
                    } else {
                        $result['Admission'] = round(0,2);
                        $result['Refund'] = round($coursePayment->amount,2);
                        $refundTotal = $refundTotal + $coursePayment->amount;
                        $cgst = $cgst - 0;
                        $sgst = $sgst - 0;
                    }
                    $result['CGST'] = round(0,2);
                    $result['SGST'] = round(0,2);
                }
                $result['Total'] = round($coursePayment->amount,2);
                $result['Date'] = date('d-m-Y',strtotime($coursePayment->payment_date));
                $resultArray[] = $result;
            }
        }
        $result = [];
        $result['Sr. No.'] = '';
        $result['UserId'] = '';
        $result['Course'] = '';
        $result['Sub Course'] = '';
        $result['Batch'] = 'Total';
        $result['Admission'] = '+'.$admissionTotal;
        $result['Refund'] = '-'.round($refundTotal,2);
        $result['CGST'] = '+'.round($cgst,2);
        $result['SGST'] = '+'.round($sgst,2);
        $result['Total'] = $admissionTotal - round($refundTotal,2) + round($cgst,2) + round($sgst,2);
        $result['Date'] = '';
        $resultArray[] = $result;
        $sheetName = 'Course Payments';
        return \Excel::create($sheetName, function($excel) use ($sheetName,$resultArray) {
            $excel->sheet($sheetName , function($sheet) use ($resultArray)
            {
                $sheet->fromArray($resultArray);
            });
        })->download('xls');
    }

    protected function showCoursePayments(){
        $allUser = [];
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type){
            $coursePayments = CoursePayment::getCoursePaymentsForSuperSuperAdmin();
        } else if(Admin::SuperAdmin == $loginUser->type){
            $coursePayments = CoursePayment::getCoursePaymentsForSuperAdmin();
        } else if(Admin::SubAdmin == $loginUser->type){
            $coursePayments = CoursePayment::getSubAdminCoursePaymentsForToday($loginUser->id);
        } else {
            return Redirect::to('admin/home');
        }
        $courses = Course::all();
        $users = User::all();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                $allUser[$user->id] = $user->f_name.' '.$user->l_name;
            }
        }
        return view('admin.course-payment.list', compact('coursePayments', 'courses', 'total', 'loginUser','allUser'));
    }

    /**
     * delete CoursePayment
     */
    protected function delete(Request $request){
        $coursePaymentId = InputSanitise::inputInt($request->get('course_payment_id'));
        if(isset($coursePaymentId)){
            $coursePayment = CoursePayment::find($coursePaymentId);
            if(is_object($coursePayment)){
                DB::beginTransaction();
                try
                {
                    $coursePayment->delete();
                    DB::commit();
                    return Redirect::to('admin/manage-course-payment')->with('message', 'Course payment deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong while delete course payment.');
                }
            }
        }
        return Redirect::to('admin/manage-course-payment');
    }

    /**
     * edit the student form.
     */
    protected function edit($id){
        $coursePayment = CoursePayment::find(json_decode($id));
        if(!is_object($coursePayment)){
            return Redirect::to('admin/home');
        }
        $courses = Course::all();
        $subcourses = [];
        $batches = [];
        $batch = Batch::find($coursePayment->batch_id);
        return view('admin.admission-form.update_new', compact('courses', 'coursePayment', 'subcourses', 'batches','batch'));
        // return view('admin.admission-form.update', compact('courses', 'coursePayment', 'subcourses', 'batches', 'courseReceipt'));
    }

    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCoursePayment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $fee = InputSanitise::inputInt($request->get('fee'));
        $gst = InputSanitise::inputInt($request->get('gst'));
        $feeType = InputSanitise::inputString($request->get('fee_type'));
        $paymentType = InputSanitise::inputInt($request->get('payment_type'));
        $amount = InputSanitise::inputInt($request->get('amount'));
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('sub_course'));
        $batchId = InputSanitise::inputInt($request->get('batch'));
        $paidAmount = InputSanitise::inputInt($request->get('paid_amount'));
        $ramRathi = InputSanitise::inputString($request->get('ram_rathi'));
        $shyamRathi = InputSanitise::inputString($request->get('shyam_rathi'));
        $giridharRathi = InputSanitise::inputString($request->get('giridhar_rathi'));
        $diptiRathi = InputSanitise::inputString($request->get('dipti_rathi'));
        $sunitaRathi = InputSanitise::inputString($request->get('sunita_rathi'));
        $caIntermediateTotal = 0;
        if(0 < $amount){
            if(0 == $paymentType){
                // if(0 >= $amount){
                //     return redirect()->back()->withErrors('please enter amount greter than 0.')->withInput();
                // }
                if('on' == $feeType){
                    if(($paidAmount + $amount) > ($fee + $gst)){
                        return redirect()->back()->withErrors('entered amount greter than total amount .')->withInput();
                    }
                } else {
                    if(($paidAmount + $amount) > $fee){
                        return redirect()->back()->withErrors('entered amount greter than total amount .')->withInput();
                    }
                }
            }
            if(empty($gst)){
                if(0 == $paymentType && $amount >= $fee){
                    return redirect()->back()->withErrors('please enter amount less than fee.')->withInput();
                }
            } else {
                if(0 == $paymentType){
                    if('on' == $feeType){
                        if($amount >= ($fee + $gst)){
                            return redirect()->back()->withErrors('please enter amount less than fee + gst.')->withInput();
                        }
                    } else {
                        if($amount >= $fee){
                            return redirect()->back()->withErrors('please enter amount less than fee.')->withInput();
                        }
                    }
                }
            }
            if(1 == $courseId && 1 == $subcourseId){
                if($ramRathi > 0){
                    $caIntermediateTotal += $ramRathi;
                }
                if($shyamRathi > 0){
                    $caIntermediateTotal += $shyamRathi;
                }
                if($giridharRathi > 0){
                    $caIntermediateTotal += $giridharRathi;
                }
                if($diptiRathi > 0){
                    $caIntermediateTotal += $diptiRathi;
                }
                if($sunitaRathi > 0){
                    $caIntermediateTotal += $sunitaRathi;
                }
                if($caIntermediateTotal > 0 && $caIntermediateTotal > $amount){
                    return redirect()->back()->withErrors('receipt by amount greter than total amount.')->withInput();
                }
                if($caIntermediateTotal > 0 && $caIntermediateTotal < $amount){
                    return redirect()->back()->withErrors('receipt by amount less than total amount.')->withInput();
                }
            }
        }
        DB::beginTransaction();
        try
        {
            $courseReceipt = CoursePayment::addAdmissionPayment($request,true);
            if(is_object($courseReceipt)){
                CoursePayment::UpdateRemainderDateByCourseIdBySubCourseIdByBatchIdByUserId($courseReceipt->course_id,$courseReceipt->sub_course_id,$courseReceipt->batch_id,$courseReceipt->user_id,$courseReceipt->id,$courseReceipt->remainder_date);
                DB::commit();

                return Redirect::to('admin/manage-course-payment')->with('message', 'Course payment updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while updated admission.');
        }
        return Redirect::to('admin/create-admission-receipt');
    }

    protected function getUserByUserId(Request $request){
        $resultArray = [];
        $users = User::getUserByUserId($request);
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                $resultArray[] = [ 'name' =>  $user->f_name.' '.$user->m_name.' '.$user->l_name, 'id' => $user->id, 'phone' => $user->phone ];
            }
        }
        return $resultArray;
    }

    protected function getUserPaymentsByCourseIdBySubcourseIdByBatchId(Request $request){
        $admissionType = $request->get('admission_type');
        if('new' == $admissionType){
            $userId = InputSanitise::inputString($request->get('user_id'));
            $newUser = User::where('id', $userId)->first();
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
            $result['course_payments'] = CoursePayment::getUserPaymentsByCourseIdBySubcourseIdByBatchId($request);
        }
        return $result;
    }


    protected function getUserTotalPaidByCourseIdBySubcourseIdByBatchId(Request $request){
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse_id'));
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        $userId = InputSanitise::inputString($request->get('user_id'));
        return CoursePayment::getUserTotalPaidByCourseIdBySubcourseIdByBatchId($courseId,$subcourseId,$batchId,$userId);
    }

    protected function getUserTotalPaidByCourseIdBySubcourseIdByBatchIdForPayments(Request $request){
        $allUser = [];
        $payments = [];
        $subcoursesName = [];
        $batchesName = [];
        $coursesName = [];
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse_id'));
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        $userId = InputSanitise::inputString($request->get('user_id'));
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $users = User::all();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                $allUser[$user->id] = $user->f_name.' '.$user->l_name;
            }
        }
        $courses = Course::all();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $courseObj){
                $coursesName[$courseObj->id] = $courseObj->name;
            }
        }

        $courseReceipts = SubCourse::all();
        if(is_object($courseReceipts) && false == $courseReceipts->isEmpty()){
            foreach($courseReceipts as $courseReceipt){
                $subcoursesName[$courseReceipt->id] = $courseReceipt->name;
            }
        }

        $batches = Batch::all();
        if(is_object($batches) && false == $batches->isEmpty()){
            foreach($batches as $batch){
                $batchesName[$batch->id] = $batch->name;
            }
        }
        $results = CoursePayment::getUserTotalPaidByCourseIdBySubcourseIdByBatchIdForPayments($courseId,$subcourseId,$batchId,$userId,$fromDate,$toDate);
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $payments[] = [
                    'id' => $result->id,
                    'user_id' => $result->user_id,
                    'name' => $allUser[$result->user_id],
                    'course' => $coursesName[$result->course_id],
                    'subcourse' => $subcoursesName[$result->sub_course_id],
                    'batch' => $batchesName[$result->batch_id],
                    'fee_type' => $result->fee_type,
                    'course_payment_type' => $result->course_payment_type,
                    'allow_to_visible' => $result->allow_to_visible,
                    'amount' => $result->amount,
                    'created_at' => date('d-m-Y',strtotime($result->payment_date)),
                    'generated_by' => $result->admin->name,
                    'comment' => $result->comment,
                ];
            }
        }
        return $payments;
    }

    protected function getUserCoursePayments(Request $request){
        $payments = [];
        $userName = '';
        $subcoursesName = [];
        $batchesName = [];
        $coursesName = [];

        $userId = InputSanitise::inputString($request->get('user_id'));
        $user = User::find($userId);
        if(is_object($user)){
            $userName = $user->f_name.' '.$user->l_name;
        }
        $courses = Course::all();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $courseObj){
                $coursesName[$courseObj->id] = $courseObj->name;
            }
        }

        $courseReceipts = SubCourse::all();
        if(is_object($courseReceipts) && false == $courseReceipts->isEmpty()){
            foreach($courseReceipts as $courseReceipt){
                $subcoursesName[$courseReceipt->id] = $courseReceipt->name;
            }
        }

        $batches = Batch::all();
        if(is_object($batches) && false == $batches->isEmpty()){
            foreach($batches as $batch){
                $batchesName[$batch->id] = $batch->name;
            }
        }

        $results = CoursePayment::getUserCoursePayments($userId);
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $payments[] = [
                    'id' => $result->id,
                    'user_id' => $result->user_id,
                    'name' => $userName,
                    'course' => $coursesName[$result->course_id],
                    'subcourse' => $subcoursesName[$result->sub_course_id],
                    'batch' => $batchesName[$result->batch_id],
                    'fee_type' => $result->fee_type,
                    'course_payment_type' => $result->course_payment_type,
                    'allow_to_visible' => $result->allow_to_visible,
                    'amount' => $result->amount,
                    'created_at' => (!empty($result->payment_date))?date('d-m-Y',strtotime($result->payment_date)):'',
                    'generated_by' => $result->admin->name,
                    'comment' => $result->comment,
                ];
            }
        }
        return $payments;
    }

    protected function toggleRecords(Request $request){
        DB::beginTransaction();
        try
        {
            $result = CoursePayment::toggleRecords($request);
            DB::commit();
            return $result;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return 'false';
        }
        return 'false';
    }

    protected function showDeletePayments(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::SuperSuperAdmin == $loginUser->type || Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $courses = Course::all();
        return view('admin.course-payment.delete-payments', compact('courses'));
    }

    protected function deletePayments(Request $request){
        DB::beginTransaction();
        try
        {
            $courseId = InputSanitise::inputInt($request->get('course'));
            $subcourseId = InputSanitise::inputInt($request->get('subcourse'));
            $batchId = InputSanitise::inputInt($request->get('batch'));
            UserCourse::deleteUserCoursesByBatchId($batchId);
            CoursePayment::hardDeleteByCourseIdBySubCourseIdByBatchId($courseId,$subcourseId,$batchId);
            DB::commit();
            return Redirect::to('admin/delete-payments')->with('message', 'Payments deleted successfully!');
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong while hard delete payments.');
        }
        return Redirect::to('admin/delete-payments');
    }

    protected function outstanding(){
        $loginUser = Auth::guard('admin')->user();
        if(Admin::Admin == $loginUser->type || Admin::SubAdmin == $loginUser->type){
            return Redirect::to('admin/home');
        }
        $courseReceiptTaxArr = [];
        $subcoursesName = [];
        $coursePaymentsArr = [];
        $usersOutstanding = [];
        $batchesName = [];
        $coursesName = [];
        $courseIds = [];
        $batchIds = [];
        $allUser = [];
        $totalOutstanding = 0;

        $courses = Course::all();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $courseObj){
                $coursesName[$courseObj->id] = $courseObj->name;
            }
        }

        $courseReceipts = SubCourse::all();
        if(is_object($courseReceipts) && false == $courseReceipts->isEmpty()){
            foreach($courseReceipts as $courseReceipt){
                $subcoursesName[$courseReceipt->id] = $courseReceipt->name;
            }
        }

        $batches = Batch::all();
        if(is_object($batches) && false == $batches->isEmpty()){
            foreach($batches as $batch){
                $courseReceiptTaxArr[$batch->course_id][$batch->sub_course_id][$batch->id] = [
                    'fee' => $batch->fee,
                    'gst' => $batch->gst,
                ];
            }
        }

        $users = User::all();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                $allUser[$user->id] = $user->f_name.' '.$user->l_name;
            }
        }

        $coursePayments = CoursePayment::getPaymentsForOutstanding();
        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
            foreach($coursePayments as $coursePayment){
                if(CoursePayment::Admission == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['paid'][] = $coursePayment->amount;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['fee_type'] = $coursePayment->fee_type;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['phone'] = $coursePayment->phone;
                } else if(CoursePayment::Discount == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['discount'][] = $coursePayment->amount;
                } else if(CoursePayment::Refund == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['refund'][] = $coursePayment->amount;
                }
                if(isset($coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id])){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['remainder_date'] = $coursePayment->remainder_date;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['comment'] = $coursePayment->comment;
                }
            }
        }
        if(count($coursePaymentsArr) > 0){
            foreach($coursePaymentsArr as $courseId => $subcourseArr){
                foreach($subcourseArr as $subcourseId => $batchesArr){
                    foreach($batchesArr as $batchId => $usersArr){
                        foreach($usersArr as $userId => $paymentsDetails){
                            if(!isset($paymentsDetails['refund']) && isset($paymentsDetails['paid'])){
                                if(1 == $paymentsDetails['fee_type']){
                                    if(isset($courseReceiptTaxArr[$courseId][$subcourseId][$batchId])){
                                        $totalFee = (int)$courseReceiptTaxArr[$courseId][$subcourseId][$batchId]['fee'] + (int)$courseReceiptTaxArr[$courseId][$subcourseId][$batchId]['gst'];
                                    }
                                } else {
                                    if(isset($courseReceiptTaxArr[$courseId][$subcourseId][$batchId])){
                                        $totalFee = (int)$courseReceiptTaxArr[$courseId][$subcourseId][$batchId]['fee'];
                                    }
                                }

                                $totalPaid = array_sum($paymentsDetails['paid']);
                                $totalDiscount = (isset($paymentsDetails['discount']))? array_sum($paymentsDetails['discount']):0;
                                $userOutstanding = $totalFee - ($totalPaid + $totalDiscount);
                                if($userOutstanding > 0){
                                    $outstanding = [];
                                    $outstanding['user_id'] = $userId;
                                    $outstanding['name'] = $allUser[$userId];
                                    $outstanding['phone'] = $paymentsDetails['phone'];
                                    $outstanding['course'] = $courseId;
                                    $outstanding['subcourse'] = $subcourseId;
                                    $outstanding['batch'] = $batchId;
                                    $outstanding['total_fee'] = $totalFee;
                                    $outstanding['paid'] = $totalPaid;
                                    $outstanding['discount'] = $totalDiscount;
                                    $outstanding['outstanding'] = $userOutstanding;
                                    $outstanding['comment'] = $paymentsDetails['comment'];
                                    $outstanding['remainder_date'] = $paymentsDetails['remainder_date'];
                                    $usersOutstanding[] = $outstanding;
                                    $batchIds[] = $batchId;
                                    $totalOutstanding += $userOutstanding;
                                }
                            }
                        }
                    }
                }
            }
        }
        if(count($usersOutstanding) > 0){
            if(count($batchIds) > 0){
                $batchesObj = Batch::find($batchIds);
                if(is_object($batchesObj) && false == $batchesObj->isEmpty()){
                    foreach($batchesObj as $batchObj){
                        $batchesName[$batchObj->id] = $batchObj->name;
                    }
                }
            }
        }

        return view('admin.course-payment.outstanding', compact('usersOutstanding', 'coursesName', 'subcoursesName', 'batchesName', 'totalOutstanding', 'courses'));
    }

    protected function getOutstandingByCourseIdBySubCourseIdByBatchId(Request $request){
        $coursePaymentsArr = [];
        $usersOutstanding = [];
        $coursesName = [];
        $subcoursesName = [];
        $batchesName = [];
        $allUser = [];
        $courseId = InputSanitise::inputString($request->get('course_id'));
        $subcourseId = InputSanitise::inputString($request->get('subcourse_id'));
        $batchId = InputSanitise::inputString($request->get('batch_id'));
        if($courseId > 0){
            $course = Course::find($courseId);
            if(is_object($course)){
                $coursesName[$course->id] = $course->name;
            }
        } else {
            $courses = Course::all();
            if(is_object($courses) && false == $courses->isEmpty()){
                foreach($courses as $courseObj){
                    $coursesName[$courseObj->id] = $courseObj->name;
                }
            }
        }
        if($subcourseId > 0){
            $subCourse = SubCourse::find($subcourseId);
            if(is_object($subCourse)){
                $subcoursesName[$subCourse->id] = $subCourse->name;
            }
        } else {
            $subCourses = SubCourse::all();
            if(is_object($subCourses) && false == $subCourses->isEmpty()){
                foreach($subCourses as $subCourse){
                    $subcoursesName[$subCourse->id] = $subCourse->name;
                }
            }
        }
        if($batchId > 0){
            $batch = Batch::find($batchId);
            if(is_object($batch)){
                $batchesName[$batch->id] = [
                        'name' => $batch->name,
                        'fee' => $batch->fee,
                        'gst' => $batch->gst,
                    ];
            }
        } else {
            $batches = Batch::all();
            if(is_object($batches) && false == $batches->isEmpty()){
                foreach($batches as $batch){
                    $batchesName[$batch->id] = [
                        'name' => $batch->name,
                        'fee' => $batch->fee,
                        'gst' => $batch->gst,
                    ];
                }
            }
        }

        $users = User::all();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                $allUser[$user->id] = $user->f_name.' '.$user->l_name;
            }
        }

        $coursePayments = CoursePayment::getOutstandingByCourseIdBySubCourseIdByBatchId($request);
        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
            foreach($coursePayments as $coursePayment){
                if(CoursePayment::Admission == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['paid'][] = $coursePayment->amount;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['fee_type'] = $coursePayment->fee_type;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['phone'] = $coursePayment->phone;
                } else if(CoursePayment::Discount == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['discount'][] = $coursePayment->amount;
                } else if(CoursePayment::Refund == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['refund'][] = $coursePayment->amount;
                }
                if(isset($coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id])){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['remainder_date'] = $coursePayment->remainder_date;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['comment'] = $coursePayment->comment;
                }
            }
        }
        if(count($coursePaymentsArr) > 0){
            foreach($coursePaymentsArr as $courseId => $subcourseArr){
                foreach($subcourseArr as $subcourseId => $batchesArr){
                    foreach($batchesArr as $batchId => $usersArr){
                        foreach($usersArr as $userId => $paymentsDetails){
                            if(!isset($paymentsDetails['refund']) && isset($paymentsDetails['paid'])){
                                if(1 == $paymentsDetails['fee_type']){
                                    if(isset($batchesName[$batchId])){
                                        $totalFee = (int)$batchesName[$batchId]['fee'] + (int)$batchesName[$batchId]['gst'];
                                    }
                                } else {
                                    if(isset($batchesName[$batchId])){
                                        $totalFee = (int)$batchesName[$batchId]['fee'];
                                    }
                                }
                                $totalPaid = array_sum($paymentsDetails['paid']);
                                $totalDiscount = (isset($paymentsDetails['discount']))? array_sum($paymentsDetails['discount']):0;
                                $userOutstanding = $totalFee - ($totalPaid + $totalDiscount);
                                if($userOutstanding > 0){
                                    $outstanding = [];
                                    $outstanding['user_id'] = $userId;
                                    $outstanding['name'] = $allUser[$userId];
                                    $outstanding['phone'] = $paymentsDetails['phone'];
                                    $outstanding['course'] = $coursesName[$courseId];
                                    $outstanding['subcourse'] = $subcoursesName[$subcourseId];
                                    $outstanding['batch'] = $batchesName[$batchId]['name'];
                                    $outstanding['total_fee'] = $totalFee;
                                    $outstanding['paid'] = $totalPaid;
                                    $outstanding['discount'] = $totalDiscount;
                                    $outstanding['outstanding'] = $userOutstanding;
                                    $outstanding['comment'] = $paymentsDetails['comment'];
                                    $outstanding['remainder_date'] = $paymentsDetails['remainder_date'];
                                    $usersOutstanding[] = $outstanding;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $usersOutstanding;
    }

    protected function downloadOutstandings(Request $request){
        $coursePaymentsArr = [];
        $usersOutstanding = [];
        $coursesName = [];
        $subcoursesName = [];
        $batchesName = [];
        $allUser = [];
        $courseId = InputSanitise::inputString($request->get('course_id'));
        $subcourseId = InputSanitise::inputString($request->get('subcourse_id'));
        $batchId = InputSanitise::inputString($request->get('batch_id'));
        if($courseId > 0){
            $course = Course::find($courseId);
            if(is_object($course)){
                $coursesName[$course->id] = $course->name;
            }
        } else {
            $courses = Course::all();
            if(is_object($courses) && false == $courses->isEmpty()){
                foreach($courses as $courseObj){
                    $coursesName[$courseObj->id] = $courseObj->name;
                }
            }
        }
        if($subcourseId > 0){
            $subCourse = SubCourse::find($subcourseId);
            if(is_object($subCourse)){
                $subcoursesName[$subCourse->id] = $subCourse->name;
            }
        } else {
            $subCourses = SubCourse::all();
            if(is_object($subCourses) && false == $subCourses->isEmpty()){
                foreach($subCourses as $subCourse){
                    $subcoursesName[$subCourse->id] = $subCourse->name;
                }
            }
        }
        if($batchId > 0){
            $batch = Batch::find($batchId);
            if(is_object($batch)){
                $batchesName[$batch->id] = [
                        'name' => $batch->name,
                        'fee' => $batch->fee,
                        'gst' => $batch->gst,
                    ];
            }
        } else {
            $batches = Batch::all();
            if(is_object($batches) && false == $batches->isEmpty()){
                foreach($batches as $batch){
                    $batchesName[$batch->id] = [
                        'name' => $batch->name,
                        'fee' => $batch->fee,
                        'gst' => $batch->gst,
                    ];
                }
            }
        }

        $users = User::all();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                $allUser[$user->id] = $user->f_name.' '.$user->l_name;
            }
        }
        $resultArray[] = ['Sr. No.','UserId','Phone','Course','Sub Course','Batch','Total Fee','Paid','Discount','Outstanding','Comment','Due Date'];
        $coursePayments = CoursePayment::getOutstandingByCourseIdBySubCourseIdByBatchId($request);
        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
            foreach($coursePayments as $coursePayment){
                if(CoursePayment::Admission == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['paid'][] = $coursePayment->amount;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['fee_type'] = $coursePayment->fee_type;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['phone'] = $coursePayment->phone;
                } else if(CoursePayment::Discount == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['discount'][] = $coursePayment->amount;
                } else if(CoursePayment::Refund == $coursePayment->course_payment_type){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['refund'][] = $coursePayment->amount;
                }
                if(isset($coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id])){
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['remainder_date'] = $coursePayment->remainder_date;
                    $coursePaymentsArr[$coursePayment->course_id][$coursePayment->sub_course_id][$coursePayment->batch_id][$coursePayment->user_id]['comment'] = $coursePayment->comment;
                }
            }
        }
        $index = 1;
        $totalOutstanding = 0;
        if(count($coursePaymentsArr) > 0){
            foreach($coursePaymentsArr as $courseId => $subcourseArr){
                foreach($subcourseArr as $subcourseId => $batchesArr){
                    foreach($batchesArr as $batchId => $usersArr){
                        foreach($usersArr as $userId => $paymentsDetails){
                            if(!isset($paymentsDetails['refund']) && isset($paymentsDetails['paid'])){
                                if(1 == $paymentsDetails['fee_type']){
                                    if(isset($batchesName[$batchId])){
                                        $totalFee = (int)$batchesName[$batchId]['fee'] + (int)$batchesName[$batchId]['gst'];
                                    }
                                } else {
                                    if(isset($batchesName[$batchId])){
                                        $totalFee = (int)$batchesName[$batchId]['fee'];
                                    }
                                }
                                $totalPaid = array_sum($paymentsDetails['paid']);
                                $totalDiscount = (isset($paymentsDetails['discount']))? array_sum($paymentsDetails['discount']):0;
                                $userOutstanding = $totalFee - ($totalPaid + $totalDiscount);
                                if($userOutstanding > 0){
                                    $outstanding = [];
                                    $outstanding['Sr. No.'] = $index;
                                    $outstanding['user_id'] = $userId.'-'.$allUser[$userId];
                                    $outstanding['phone'] = $paymentsDetails['phone'];
                                    $outstanding['course'] = $coursesName[$courseId];
                                    $outstanding['subcourse'] = $subcoursesName[$subcourseId];
                                    $outstanding['batch'] = $batchesName[$batchId]['name'];
                                    $outstanding['total_fee'] = $totalFee;
                                    $outstanding['paid'] = $totalPaid;
                                    $outstanding['discount'] = $totalDiscount;
                                    $outstanding['outstanding'] = $userOutstanding;
                                    $outstanding['comment'] = $paymentsDetails['comment'];
                                    $outstanding['remainder_date'] = $paymentsDetails['remainder_date'];
                                    $resultArray[] = $outstanding;
                                    $totalOutstanding = ($totalOutstanding + $userOutstanding);
                                    $index++;
                                }
                            }
                        }
                    }
                }
            }
        }

        $result = [];
        $result['Sr. No.'] = '';
        $result['UserId'] = '';
        $result['Phone'] = '';
        $result['Course'] = '';
        $result['Sub Course'] = '';
        $result['Batch'] = '';
        $result['Total Fee'] = '';
        $result['Paid'] = '';
        $result['Discount'] = 'Total Outstanding';
        $result['Outstanding'] = $totalOutstanding;
        $result['Comment'] = '';
        $result['Due Date'] = '';
        $resultArray[] = $result;
        $sheetName = 'Outstandings';
        return \Excel::create($sheetName, function($excel) use ($sheetName,$resultArray) {
            $excel->sheet($sheetName , function($sheet) use ($resultArray)
            {
                $sheet->fromArray($resultArray);
            });
        })->download('xls');
    }

    protected function userCourses(){
        return view('admin.admission-form.user_courses');
    }

}
