<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CoursePayment;
use App\Models\SubCourse;

class SendRemainderMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendremaindermessage:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Remainder Message';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);
        $courseReceiptTaxArr = [];
        $courseReceipts = SubCourse::all();
        if(is_object($courseReceipts) && false == $courseReceipts->isEmpty()){
            foreach($courseReceipts as $courseReceipt){
                $courseReceiptTaxArr[$courseReceipt->course_id][$courseReceipt->id] = [
                    'fee' => $courseReceipt->fee,
                    'gst' => $courseReceipt->gst,
                    'name' => $courseReceipt->name,
                ];
            }
        }

        $remainderPayments = CoursePayment::where('payment_type', 0)->where('allow_to_visible', 1)->where('remainder_date','=',date('d-m-Y'))->get();
        if(is_object($remainderPayments) && false == $remainderPayments->isEmpty()){
            foreach($remainderPayments as $remainderPayment){
                $courseId = $remainderPayment->course_id;
                $subcourseId = $remainderPayment->sub_course_id;
                $batchId = $remainderPayment->batch_id;
                $userId = $remainderPayment->user_id;
                $paidAmount = 0;
                $discountAmount = 0;
                $feeType = 0;
                $totalFee = 0;
                $isRefundAmount = 0;
                $userPayments =  CoursePayment::getUsersPaymentsByCourseIdBySubCourseIdByBatchIdByUserId($courseId,$subcourseId,$batchId,$userId);
                if(is_object($userPayments) && false == $userPayments->isEmpty()){
                    foreach($userPayments as $userPayment){
                        $feeType = $userPayment->fee_type;
                        if(1 == $userPayment->course_payment_type){
                            $paidAmount = (int) $paidAmount + (int) $userPayment->amount;
                        } else if(2 == $userPayment->course_payment_type){
                            $discountAmount = (int) $discountAmount + (int) $userPayment->amount;
                        } else if(3 == $userPayment->course_payment_type) {
                            $isRefundAmount = 1;
                        }
                    }
                }
                if(0 == $isRefundAmount){
                    if(1 == $feeType){
                        $totalFee = $courseReceiptTaxArr[$courseId][$subcourseId]['fee'] + $courseReceiptTaxArr[$courseId][$subcourseId]['gst'];
                    } else {
                        $totalFee = $courseReceiptTaxArr[$courseId][$subcourseId]['fee'];
                    }
                    $outstandingAmount = $totalFee-($paidAmount+$discountAmount);
                    if($outstandingAmount > 0){
                        $subcourseName = $courseReceiptTaxArr[$courseId][$subcourseId]['name'];
                        // // Account details
                        $apiKey = urlencode(config('custom.sms_key'));
                        // Message details
                        $mobileNo = '91'.$remainderPayment->phone;
                        $numbers = array($mobileNo);
                        $sender = urlencode('TXTLCL');
                        $userMessage = 'Dear '.$remainderPayment->f_name.' '.$remainderPayment->l_name.', Please pay remaining amount Rs. '.$outstandingAmount.' for course: '. $subcourseName .'. -- RCF';

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
                        $this->info('sent sms to '.$remainderPayment->f_name.' '.$remainderPayment->l_name.'</br>');
                    }
                }
            }
        }
    }
}
