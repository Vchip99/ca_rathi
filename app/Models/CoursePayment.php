<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Course;
use App\Models\SubCourse;
use App\Models\Batch;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\Admin;
use Redirect, DB, Auth;

class CoursePayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receipt_id','f_name', 'm_name', 'l_name', 'user_id', 'phone', 'course_id', 'sub_course_id','batch_id', 'fee_type', 'payment_type','cheque_no', 'amount','all_name', 'ram_rathi', 'shyam_rathi', 'giridhar_rathi', 'dipti_rathi', 'sunita_rathi', 'remainder_date', 'comment','course_payment_type','generated_by','is_deleted','allow_to_visible','payment_date'
    ];

    const Admission = 1;
    const Discount = 2;
    const Refund = 3;

    /**
     *  create/update course payment
     */
    protected static function addAdmissionPayment( Request $request, $isUpdate = false){
        $fName = InputSanitise::inputString($request->get('f_name'));
        $mName = InputSanitise::inputString($request->get('m_name'));
        $lName = InputSanitise::inputString($request->get('l_name'));
        $userId = InputSanitise::inputString($request->get('user_id'));
        $phone = InputSanitise::inputString($request->get('phone'));
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('sub_course'));
        $batchId = InputSanitise::inputInt($request->get('batch'));
        $feeType = InputSanitise::inputString($request->get('fee_type'));
        $paymentType = InputSanitise::inputInt($request->get('payment_type'));
        $amount = InputSanitise::inputInt($request->get('amount'));
        $receiptBy = InputSanitise::inputString($request->get('receipt_by'));
        $remainderDate = $request->get('remainder_date');
        $comment = InputSanitise::inputString($request->get('comment'));
        $chequeNo = InputSanitise::inputString($request->get('cheque_no'));

        $ramRathi = InputSanitise::inputString($request->get('ram_rathi'));
        $shyamRathi = InputSanitise::inputString($request->get('shyam_rathi'));
        $giridharRathi = InputSanitise::inputString($request->get('giridhar_rathi'));
        $diptiRathi = InputSanitise::inputString($request->get('dipti_rathi'));
        $sunitaRathi = InputSanitise::inputString($request->get('sunita_rathi'));
        $paymentDate = $request->get('payment_date');
        $coursePaymentId = InputSanitise::inputInt($request->get('course_payment_id'));

        $courseObj = Course::find($courseId);
        if(is_object($courseObj)){
            $courseName = $courseObj->name;
        } else {
            $courseName = '';
        }

        $subCourseObj = SubCourse::find($subcourseId);
        if(is_object($subCourseObj)){
            $subCourseName = $subCourseObj->name;
        } else {
            $subCourseName = '';
        }

        $batchObj = Batch::find($batchId);
        if(is_object($batchObj)){
            $batchName = $batchObj->name;
        } else {
            $batchName = '';
        }
        if(false == $isUpdate && empty($coursePaymentId)){
            if(1 == (int)$courseId && 1 == (int)$subcourseId){
                $newReceiptId = '';

                if(!empty($ramRathi)){
                    $newReceiptId = rand(10000000, 99999999);
                }

                if(!empty($shyamRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }

                if(!empty($giridharRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }
                if(!empty($diptiRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }
                if(!empty($sunitaRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }
            } else {
                $newReceiptId = rand(10000000, 99999999);
            }

            $coursePayment = new static;
            $coursePayment->receipt_id = $newReceiptId;
        } else {
            $coursePayment = static::find($coursePaymentId);

            if(1 == (int)$courseId && 1 == (int)$subcourseId && $coursePayment->course_id != $courseId && $coursePayment->sub_course_id != $subcourseId ){
                $newReceiptId = '';

                if(!empty($ramRathi)){
                    $newReceiptId = rand(10000000, 99999999);
                }

                if(!empty($shyamRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }

                if(!empty($giridharRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }
                if(!empty($diptiRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }
                if(!empty($sunitaRathi)){
                    $newReceiptId .= ','.rand(10000000, 99999999);
                }
                $coursePayment->receipt_id = $newReceiptId;
            } else if($coursePayment->course_id != $courseId && $coursePayment->sub_course_id != $subcourseId ){
                $coursePayment->receipt_id = rand(10000000, 99999999);
            }
        }
        $coursePayment->f_name = $fName;
        $coursePayment->m_name = $mName;
        $coursePayment->l_name = $lName;
        $coursePayment->user_id = $userId;
        $coursePayment->phone = $phone;
        $coursePayment->course_id = $courseId;
        $coursePayment->sub_course_id = $subcourseId;
        $coursePayment->batch_id = $batchId;
        $coursePayment->fee_type = ('on' == $feeType)?1:0;
        $coursePayment->payment_type = $paymentType;
        $coursePayment->cheque_no = $chequeNo;
        $coursePayment->amount = $amount;
        $coursePayment->all_name = $courseName.'|'.$subCourseName.'|'.$batchName;
        if(1 == $courseId && 1 == $subcourseId){
            if(!empty($ramRathi)){
                $coursePayment->ram_rathi = $ramRathi;
            }
            if(!empty($shyamRathi)){
                $coursePayment->shyam_rathi = $shyamRathi;
            }
            if(!empty($giridharRathi)){
                $coursePayment->giridhar_rathi = $giridharRathi;
            }
            if(!empty($diptiRathi)){
                $coursePayment->dipti_rathi = $diptiRathi;
            }
            if(!empty($sunitaRathi)){
                $coursePayment->sunita_rathi = $sunitaRathi;
            }
        } else {
            $coursePayment->ram_rathi = '';
            $coursePayment->shyam_rathi = '';
            $coursePayment->giridhar_rathi = '';
            $coursePayment->dipti_rathi = '';
            $coursePayment->sunita_rathi = '';
        }
        $coursePayment->remainder_date = $remainderDate;
        $coursePayment->comment = $comment;
        $coursePayment->course_payment_type = self::Admission;
        $coursePayment->generated_by = Auth::guard('admin')->user()->id;
        $coursePayment->is_deleted = 0;
        $coursePayment->allow_to_visible = 1;
        $coursePayment->payment_date = date('Y-m-d',strtotime($paymentDate));
        $coursePayment->save();

        return $coursePayment;
    }

    protected static function getUserPaymentsByCourseIdBySubcourseIdByBatchId(Request $request){
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse_id'));
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        $userId = InputSanitise::inputString($request->get('user_id'));
        $paymentId = InputSanitise::inputInt($request->get('payment_id'));
        return static::where('user_id', $userId)->where('course_id', $courseId)->where('sub_course_id', $subcourseId)->where('batch_id', $batchId)->where('id', '!=', $paymentId)->get();
    }

    protected static function getUserTotalPaidByCourseIdBySubcourseIdByBatchId($courseId,$subcourseId,$batchId,$userId){
        return static::where('user_id', $userId)->where('course_payment_type', self::Admission)->where('course_id', $courseId)->where('sub_course_id', $subcourseId)->where('batch_id', $batchId)->get();
    }

    protected static function getUserTotalPaidByCourseIdBySubcourseIdByBatchIdForPayments($courseId,$subcourseId,$batchId,$userId,$fromDate = NULL,$toDate = NULL){
        $result = static::where('course_payment_type','!=',self::Discount)->where('is_deleted', 0)->where('allow_to_visible', 1);
        if($courseId > 0){
            $result->where('course_id', $courseId);
        }
        if($subcourseId > 0){
            $result->where('sub_course_id', $subcourseId);
        }
        if($batchId > 0){
            $result->where('batch_id', $batchId);
        }
        if(is_string($userId) && !empty($userId) && 'All' != $userId){
            $result->where('user_id', $userId);
        }
        if(is_string($fromDate)){
            $result->where('payment_date','>=', $fromDate);
        }
        if(is_string($toDate)){
            $result->where('payment_date','<=', $toDate);
        }
        return $result->orderBy('id', 'desc')->get();
    }

    protected static function getUserCoursePayments($userId){
        $result = static::where('course_payment_type','!=',self::Discount)->where('is_deleted', 0)->where('allow_to_visible', 1);
        if(is_string($userId) && !empty($userId)){
            $result->where('user_id', $userId);
        }
        return $result->orderBy('batch_id', 'asc')->get();
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function subCourse(){
        return $this->belongsTo(SubCourse::class, 'sub_course_id');
    }

    public function batch(){
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'generated_by');
    }

    protected static function getUsersPaymentsByCourseIdBySubCourseIdByBatchIdByUserId($courseId,$subcourseId,$batchId,$userId){
        return static::where('user_id', $userId)->where('course_id', $courseId)->where('sub_course_id', $subcourseId)->where('batch_id', $batchId)->where('payment_type', 0)->where('is_deleted', 0)->where('allow_to_visible', 1)->get();
    }

    protected static function softDeleteByBatchId($batchId){
        $coursePayments = static::where('batch_id', $batchId)->get();
        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
            foreach($coursePayments as $coursePayment){
                if(self::Discount == $coursePayment->course_payment_type){
                    // only discount record will be hard deleted
                    $coursePayment->delete();
                } else {
                    // admission and refund record will be soft deleted
                    $coursePayment->is_deleted = 1;
                    $coursePayment->save();
                }
            }
        }
        return;
    }

    protected static function softDeleteBySubCourseId($subCourseId){
        $coursePayments = static::where('sub_course_id', $subCourseId)->get();
        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
            foreach($coursePayments as $coursePayment){
                if(self::Discount == $coursePayment->course_payment_type){
                    // only discount record will be hard deleted
                    $coursePayment->delete();
                } else {
                    // admission and refund record will be soft deleted
                    $coursePayment->is_deleted = 1;
                    $coursePayment->save();
                }
            }
        }
        return;
    }

    protected static function softDeleteByCourseId($courseId){
        $coursePayments = static::where('course_id', $courseId)->get();
        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
            foreach($coursePayments as $coursePayment){
                if(self::Discount == $coursePayment->course_payment_type){
                    // only discount record will be hard deleted
                    $coursePayment->delete();
                } else {
                    // admission and refund record will be soft deleted
                    $coursePayment->is_deleted = 1;
                    $coursePayment->save();
                }
            }
        }
        return;
    }

    protected static function getAllDiscounts(){
        return static::where('course_payment_type',self::Discount)->where('is_deleted', 0)->where('allow_to_visible', 1)->orderBy('id', 'desc')->get();
    }

    protected static function getAllRefunds(){
        return static::where('course_payment_type',self::Refund)->where('is_deleted', 0)->where('allow_to_visible', 1)->orderBy('id', 'desc')->get();
    }

    protected static function getSubAdminRefundsForToday($loginId){
        $today = date('Y-m-d');
        return static::where('course_payment_type',self::Refund)->where('is_deleted', 0)->where('allow_to_visible', 1)->where('generated_by', $loginId)->where('payment_date','>=', $today)->where('payment_date','<=', $today)->orderBy('id', 'desc')->get();
    }

    protected static function getSubAdminCoursePaymentsForToday($loginId){
        $today = date('Y-m-d');
        return static::where('course_payment_type','!=',self::Discount)->where('is_deleted', 0)->where('allow_to_visible', 1)->where('generated_by', $loginId)->where('payment_date','>=', $today)->where('payment_date','<=', $today)->orderBy('id', 'desc')->get();
    }

    protected static function getCoursePaymentsForSuperAdmin(){
        return static::where('course_payment_type','!=',self::Discount)->where('is_deleted', 0)->where('allow_to_visible', 1)->orderBy('id', 'desc')->get();
    }

    protected static function getCoursePaymentsForSuperSuperAdmin(){
        return static::where('course_payment_type','!=',self::Discount)->where('is_deleted', 0)->orderBy('id', 'desc')->get();
    }

    protected static function createDiscount(Request $request){
        $userId = InputSanitise::inputInt($request->get('user'));
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse'));
        $batchId = InputSanitise::inputInt($request->get('batch'));

        $discount = InputSanitise::inputInt($request->get('discount'));
        $remark = InputSanitise::inputString($request->get('remark'));

        $courseObj = Course::find($courseId);
        if(is_object($courseObj)){
            $courseName = $courseObj->name;
        } else {
            $courseName = '';
        }

        $subCourseObj = SubCourse::find($subcourseId);
        if(is_object($subCourseObj)){
            $subCourseName = $subCourseObj->name;
        } else {
            $subCourseName = '';
        }

        $batchObj = Batch::find($batchId);
        if(is_object($batchObj)){
            $batchName = $batchObj->name;
        } else {
            $batchName = '';
        }

        $userObj = User::find($userId);
        if(is_object($userObj)){
            $userIdStr = $userObj->id;
            $fName = $userObj->f_name;
            $mName = $userObj->m_name;
            $lName = $userObj->l_name;
            $phone = $userObj->phone;
        } else {
            $userIdStr = '';
            $fName = '';
            $mName = '';
            $lName = '';
            $phone = '';
        }

        $coursePayment = new static;
        $coursePayment->receipt_id = ' ';
        $coursePayment->f_name = $fName;
        $coursePayment->m_name = $mName;
        $coursePayment->l_name = $lName;
        $coursePayment->user_id = $userIdStr;
        $coursePayment->phone = $phone;
        $coursePayment->course_id = $courseId;
        $coursePayment->sub_course_id = $subcourseId;
        $coursePayment->batch_id = $batchId;
        $coursePayment->fee_type = 0;
        $coursePayment->payment_type = 0;
        $coursePayment->cheque_no = '';
        $coursePayment->amount = $discount;
        $coursePayment->all_name = $courseName.'|'.$subCourseName.'|'.$batchName;
        $coursePayment->comment = $remark;
        $coursePayment->course_payment_type = self::Discount;
        $coursePayment->generated_by = Auth::guard('admin')->user()->id;
        $coursePayment->is_deleted = 0;
        $coursePayment->allow_to_visible = 1;
        $coursePayment->payment_date = date('Y-m-d');
        $coursePayment->save();

        return $coursePayment;
    }

    protected static function createRefund(Request $request){
        $userId = InputSanitise::inputString($request->get('user'));
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse'));
        $batchId = InputSanitise::inputInt($request->get('batch'));
        $refund = InputSanitise::inputInt($request->get('refund'));
        $remark = InputSanitise::inputString($request->get('remark'));
        $ramRathi = InputSanitise::inputInt($request->get('ram_rathi'));
        $shyamRathi = InputSanitise::inputInt($request->get('shyam_rathi'));
        $giridharRathi = InputSanitise::inputInt($request->get('giridhar_rathi'));
        $diptiRathi = InputSanitise::inputInt($request->get('dipti_rathi'));
        $sunitaRathi = InputSanitise::inputInt($request->get('sunita_rathi'));
        $totalPaid = InputSanitise::inputInt($request->get('total_paid'));
        $feeType = InputSanitise::inputInt($request->get('fee_type'));

        // $lastPayment = static::where('course_payment_type','!=',self::Discount)->orderBy('id', 'desc')->first();

        // if( is_object($lastPayment) && 1 == $lastPayment->course_id && 1 == $lastPayment->sub_course_id){
        //     if(empty($lastPayment->receipt_id)){
        //         $lastReceiptId = 100000;
        //     } else {
        //         $lastReceiptId = array_reverse(explode(',', $lastPayment->receipt_id))[0];
        //     }
        //     if(1 == $courseId && 1 == $subcourseId){
        //         $newReceiptId = '';
        //         if($ramRathi > 0){
        //             $lastReceiptId = $lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = $lastReceiptId;
        //             }
        //         }
        //         if($shyamRathi > 0){
        //             $lastReceiptId = $lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = $lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.$lastReceiptId;
        //             }
        //         }
        //         if($giridharRathi > 0){
        //             $lastReceiptId = $lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = $lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.$lastReceiptId;
        //             }
        //         }
        //         if($diptiRathi > 0){
        //             $lastReceiptId = $lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = $lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.$lastReceiptId;
        //             }
        //         }
        //         if($sunitaRathi > 0){
        //             $lastReceiptId = $lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = $lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.$lastReceiptId;
        //             }
        //         }
        //     } else {
        //         $newReceiptId = $lastReceiptId + 1;
        //     }
        // } else {
        //     if( !is_object($lastPayment)){
        //         $lastReceiptId = 100000;
        //     } else {
        //         $lastReceiptId = $lastPayment->receipt_id;
        //     }

        //     if(1 == (int)$courseId && 1 == (int)$subcourseId){
        //         $newReceiptId = '';

        //         if($ramRathi > 0){
        //             $lastReceiptId = (int)$lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = (int)$lastReceiptId;
        //             }
        //         }

        //         if($shyamRathi > 0){
        //             $lastReceiptId = (int)$lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = (int)$lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.(int)$lastReceiptId;
        //             }
        //         }

        //         if($giridharRathi > 0){
        //             $lastReceiptId = (int)$lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = (int)$lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.(int)$lastReceiptId;
        //             }
        //         }
        //         if($diptiRathi > 0){
        //             $lastReceiptId = (int)$lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = (int)$lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.(int)$lastReceiptId;
        //             }
        //         }
        //         if($sunitaRathi > 0){
        //             $lastReceiptId = (int)$lastReceiptId + 1;
        //             if(empty($newReceiptId)){
        //                 $newReceiptId = (int)$lastReceiptId;
        //             } else {
        //                 $newReceiptId .= ','.(int)$lastReceiptId;
        //             }
        //         }
        //     } else {
        //         $newReceiptId = (int)$lastReceiptId + 1;
        //     }
        // }

        if(1 == (int)$courseId && 1 == (int)$subcourseId){
            $newReceiptId = '';

            if(!empty($ramRathi)){
                $newReceiptId = rand(10000000, 99999999);
            }

            if(!empty($shyamRathi)){
                $newReceiptId .= ','.rand(10000000, 99999999);
            }

            if(!empty($giridharRathi)){
                $newReceiptId .= ','.rand(10000000, 99999999);
            }
            if(!empty($diptiRathi)){
                $newReceiptId .= ','.rand(10000000, 99999999);
            }
            if(!empty($sunitaRathi)){
                $newReceiptId .= ','.rand(10000000, 99999999);
            }
        } else {
            $newReceiptId = rand(10000000, 99999999);
        }

        $courseObj = Course::find($courseId);
        if(is_object($courseObj)){
            $courseName = $courseObj->name;
        } else {
            $courseName = '';
        }

        $subCourseObj = SubCourse::find($subcourseId);
        if(is_object($subCourseObj)){
            $subCourseName = $subCourseObj->name;
        } else {
            $subCourseName = '';
        }

        $batchObj = Batch::find($batchId);
        if(is_object($batchObj)){
            $batchName = $batchObj->name;
        } else {
            $batchName = '';
        }

        $userObj = User::where('id',$userId)->first();
        if(is_object($userObj)){
            $userIdStr = $userObj->id;
            $fName = $userObj->f_name;
            $mName = $userObj->m_name;
            $lName = $userObj->l_name;
            $phone = $userObj->phone;
        } else {
            $userIdStr = '';
            $fName = '';
            $mName = '';
            $lName = '';
            $phone = '';
        }

        $coursePayment = new static;
        $coursePayment->receipt_id = $newReceiptId;
        $coursePayment->f_name = $fName;
        $coursePayment->m_name = $mName;
        $coursePayment->l_name = $lName;
        $coursePayment->user_id = $userIdStr;
        $coursePayment->phone = $phone;
        $coursePayment->course_id = $courseId;
        $coursePayment->sub_course_id = $subcourseId;
        $coursePayment->batch_id = $batchId;
        $coursePayment->fee_type = $feeType;
        $coursePayment->payment_type = 0;
        $coursePayment->cheque_no = '';
        $coursePayment->amount = $refund;
        $coursePayment->all_name = $courseName.'|'.$subCourseName.'|'.$batchName;
        if($ramRathi > 0){
            $coursePayment->ram_rathi = ($ramRathi/$totalPaid)*$refund;
        }
        if($shyamRathi > 0){
            $coursePayment->shyam_rathi = ($shyamRathi/$totalPaid)*$refund;
        }
        if($giridharRathi > 0){
            $coursePayment->giridhar_rathi = ($giridharRathi/$totalPaid)*$refund;
        }
        if($diptiRathi > 0){
            $coursePayment->dipti_rathi = ($diptiRathi/$totalPaid)*$refund;
        }
        if($sunitaRathi > 0){
            $coursePayment->sunita_rathi = ($sunitaRathi/$totalPaid)*$refund;
        }
        $coursePayment->comment = $remark;
        $coursePayment->course_payment_type = self::Refund;
        $coursePayment->generated_by = Auth::guard('admin')->user()->id;
        $coursePayment->is_deleted = 0;
        $coursePayment->allow_to_visible = 1;
        $coursePayment->payment_date = date('Y-m-d');
        $coursePayment->save();

        UserCourse::deleteUserCourseByCourseIdBySubCourseIdByBatchId($userObj->id,$courseId,$subcourseId,$batchId);
        return $coursePayment;
    }

    protected static function downloadCoursePayments($request){
        $fromDate = $request->get('from');
        $toDate = $request->get('to');
        $userId = InputSanitise::inputString($request->get('user'));
        $courseId = InputSanitise::inputInt($request->get('course'));
        $subcourseId = InputSanitise::inputInt($request->get('subcourse'));
        $batchId = InputSanitise::inputInt($request->get('batch'));
        return self::getUserTotalPaidByCourseIdBySubcourseIdByBatchIdForPayments($courseId,$subcourseId,$batchId,$userId,$fromDate,$toDate);
    }

    protected static function toggleRecords($request){
        $paymentId = InputSanitise::inputInt($request->get('course_payment_id'));
        $coursePayment = static::find($paymentId);
        if(is_object($coursePayment)){
            if( 1 == $coursePayment->allow_to_visible){
                $coursePayment->allow_to_visible = 0;
            } else {
                $coursePayment->allow_to_visible = 1;
            }
            $coursePayment->save();
            return 'true';
        }
        return 'false';
    }

    protected static function hardDeleteByCourseIdBySubCourseIdByBatchId($courseId,$subcourseId,$batchId){
        // hard deleted
        static::where('course_id', $courseId)->where('sub_course_id', $subcourseId)->where('batch_id', $batchId)->delete();
        return;
    }

    protected static function getPaymentsForOutstanding(){
        return static::where('payment_type', 0)->where('is_deleted', 0)->where('allow_to_visible', 1)->get();
    }

    protected static function getOutstandingByCourseIdBySubCourseIdByBatchId(Request $request){
        $courseId = InputSanitise::inputString($request->get('course_id'));
        $subcourseId = InputSanitise::inputString($request->get('subcourse_id'));
        $batchId = InputSanitise::inputString($request->get('batch_id'));
        $dueStatus = InputSanitise::inputString($request->get('due_status'));

        $resultQuery = static::where('payment_type', 0)->where('is_deleted', 0)->where('allow_to_visible', 1);
        if($courseId > 0){
            $resultQuery->where('course_id', $courseId);
        }
        if($subcourseId > 0){
            $resultQuery->where('sub_course_id', $subcourseId);
        }
        if($batchId > 0){
            $resultQuery->where('batch_id', $batchId);
        }
        if(0 == $dueStatus){
            $resultQuery->where('remainder_date', '=', date('d-m-Y'));
        }
        return $resultQuery->get();
    }

    protected static function UpdateRemainderDateByCourseIdBySubCourseIdByBatchIdByUserId($courseId,$subcourseId,$batchId,$userId,$id,$remainderDate){
        $coursePayments = static::where('user_id', $userId)->where('course_id', $courseId)->where('sub_course_id', $subcourseId)->where('batch_id', $batchId)->where('course_payment_type',self::Admission)->where('payment_type', 0)->where('id', '!=', $id)->get();
        if(is_object($coursePayments) && false == $coursePayments->isEmpty()){
            foreach($coursePayments as $coursePayment){
                $coursePayment->remainder_date = $remainderDate;
                $coursePayment->save();
            }
        }
    }

    protected static function getCoursePaymentsByAdminId($adminId){
        return static::where('generated_by', $adminId)->get();
    }
}