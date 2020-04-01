@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Course Payment </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> Course Payment </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
    <div class="form-group row">
      <div class="col-sm-6">
        @if(1 == $coursePayment->course_payment_type)
          <label for="fee" class="col-form-label">Receipt Type : Admission</label>
        @else
          <label for="fee" class="col-form-label">Receipt Type : Refund</label>
        @endif
      </div>
      <div class="offset-sm-2 col-sm-3 pull-right" title="Submit" >
        <a href="{{ url('admin/manage-course-payment')}}" class="btn btn-primary" >Back</a>
      </div>
    </div>
    <div class="form-group row">
      <label for="fee" class="col-sm-2 col-form-label">Name:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="f_name" id="f_name" value="{{$coursePayment->f_name}}" placeholder="first name" required="true" readonly>
      </div>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="m_name" id="m_name" value="{{$coursePayment->m_name}}" placeholder="middle name" required="true" readonly>
      </div>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="l_name" id="l_name" value="{{$coursePayment->l_name}}" placeholder="last name" required="true" readonly>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('user_id')) has-error @endif">
      <label for="user_id" class="col-sm-2 col-form-label">User Id:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="user_id" id="user_id" value="{{$coursePayment->user_id}}" placeholder="user id" required="true" readonly>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('phone')) has-error @endif">
      <label for="phone" class="col-sm-2 col-form-label">Phone:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="phone" id="phone" value="{{$coursePayment->phone}}" placeholder="phone" required="true" readonly>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        <select id="course" class="form-control" name="course" required title="Course" disabled>
            <option value="">Select Course</option>
            @if(count($courses) > 0)
              @foreach($courses as $course)
                @if($coursePayment->course_id == $course->id)
                  <option value="{{$course->id}}" selected>{{$course->name}}</option>
                @else
                  <option value="{{$course->id}}">{{$course->name}}</option>
                @endif
              @endforeach
            @endif
        </select>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('sub_course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Course Name:</label>
      <div class="col-sm-3">
        <select id="subcourse" class="form-control" name="sub_course" required title="Sub Course" disabled>
          <option value="">Select Sub Course</option>
            @if(count($subcourses) > 0)
              @foreach($subcourses as $subcourse)
                @if($coursePayment->sub_course_id == $subcourse->id)
                  <option value="{{$subcourse->id}}" selected>{{$subcourse->name}}</option>
                @else
                  <option value="{{$subcourse->id}}">{{$subcourse->name}}</option>
                @endif
              @endforeach
            @endif
        </select>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label">Batch Name:</label>
      <div class="col-sm-3">
        <select id="batch" class="form-control" name="batch" required title="Batch" disabled>
          <option value="">Select Batch</option>
          @if(count($batches) > 0)
            @foreach($batches as $batch)
              @if($coursePayment->batch_id == $batch->id)
                <option value="{{$batch->id}}" selected>{{$batch->name}}</option>
              @else
                <option value="{{$batch->id}}">{{$batch->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('fee')) has-error @endif">
      <label for="fee" class="col-sm-2 col-form-label">Fee:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="fee" id="fee" value="{{$courseReceipt->fee}}" placeholder="Fee" required="true" readonly>
        @if($errors->has('fee')) <p class="help-block">{{ $errors->first('fee') }}</p> @endif
      </div>
    </div>
    @if($courseReceipt->gst > 0)
    <div id="gstDiv">
      <div class="form-group row @if ($errors->has('gst')) has-error @endif">
        <label for="gst" class="col-sm-2 col-form-label">Gst:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="gst" id="gst" value="{{$courseReceipt->gst}}" placeholder="Gst"  readonly>
        </div>
      </div>
      <div class="form-group row">
        <label for="gst" class="col-sm-2 col-form-label">Type:</label>
        <div class="col-sm-2">
          <input type="checkbox" name="fee_type" id="fee_type" @if(1 == $coursePayment->fee_type) checked="true" @endif disabled>
        </div>
      </div>
    </div>
    @endif
    <div class="form-group row">
      <label for="fee_method" class="col-sm-2 col-form-label">&nbsp;</label>
      <div class="col-sm-2">
        <label><input type="radio" name="fee_method" value="1"  @if(empty($coursePayment->cheque_no)) checked="true" @endif disabled> Cash </label>
      </div>
      <div class="col-sm-2">
        <label><input type="radio" name="fee_method" value="0" @if(!empty($coursePayment->cheque_no)) checked="true" @endif disabled> Cheque </label>
      </div>
    </div>
    @if(!empty($coursePayment->cheque_no))
    <div class="form-group row">
      <label for="cheque_no" class="col-sm-2 col-form-label">Cheque No:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="cheque_no" id="cheque_no" value="{{$coursePayment->cheque_no}}" readonly>
      </div>
    </div>
    @endif
    <div class="form-group row @if ($errors->has('payment_type')) has-error @endif">
      <label for="payment_type" class="col-sm-2 col-form-label">Payment Type:</label>
      <div class="col-sm-2">
        <label><input type="radio" name="payment_type" value="1" @if(1 == $coursePayment->payment_type) checked="true" @endif disabled> Full Payment</label>
      </div>
      <div class="col-sm-2">
        <label><input type="radio" name="payment_type" value="0" @if(0 == $coursePayment->payment_type) checked="true" @endif disabled> Partial Payment</label>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('amount')) has-error @endif">
      <label for="amount" class="col-sm-2 col-form-label">Amount Rs:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" id="amount" name="amount" value="{{$coursePayment->amount}}" placeholder="Amount" required readonly>
        @if($errors->has('amount')) <p class="help-block">{{ $errors->first('amount') }}</p> @endif
      </div>
      <div class="col-sm-6">
          <textarea class="form-control" name="comment" placeholder="enter comment" readonly>{{$coursePayment->comment}}</textarea>
      </div>
    </div>
    @if(0 == $coursePayment->payment_type)
    <div id="remainderDiv" class="form-group row">
      <label for="remainder_date" class="col-sm-2 col-form-label">Remainder Date:</label>
      <div class="col-sm-3">
          <input type="text" id="remainder_date" class="form-control" name="remainder_date" value="{{$coursePayment->remainder_date}}" readonly>
      </div>
    </div>
    @endif
    @if(1 == $coursePayment->course_id && 1 == $coursePayment->sub_course_id)
      <div id="multipleReceipt" class="">
        <div class="form-group row ">
          <label for="course" class="col-sm-2 col-form-label">Receipt By Name:</label>
          <div class="col-sm-3"></div>
        </div>
        <div class="form-group row ">
          <label for="course" class="col-sm-2 col-form-label">Ram Rathi:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="ram_rathi" name="ram_rathi" value="{{$coursePayment->ram_rathi}}" placeholder="Amount" readonly>
          </div>
        </div>
        <div class="form-group row ">
          <label for="course" class="col-sm-2 col-form-label">Shyam Rathi:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="shyam_rathi" name="shyam_rathi" value="{{$coursePayment->shyam_rathi}}" placeholder="Amount" readonly>
          </div>
        </div>
        <div class="form-group row ">
          <label for="course" class="col-sm-2 col-form-label">Giridhar Rathi:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="giridhar_rathi" name="giridhar_rathi" value="{{$coursePayment->giridhar_rathi}}" placeholder="Amount" readonly>
          </div>
        </div>
        <div class="form-group row ">
          <label for="course" class="col-sm-2 col-form-label">Dipti Rathi:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="dipti_rathi" name="dipti_rathi" value="{{$coursePayment->dipti_rathi}}" placeholder="Amount" readonly>
          </div>
        </div>
        <div class="form-group row ">
          <label for="course" class="col-sm-2 col-form-label">Sunita Rathi:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="sunita_rathi" name="sunita_rathi" value="{{$coursePayment->sunita_rathi}}" placeholder="Amount" readonly>
          </div>
        </div>
      </div>
    @else
      <div id="singleReceipt">
      <div class="form-group row @if ($errors->has('receipt_by')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Receipt By Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" id="receipt_by" name="receipt_by" value="{{$coursePayment->subcourse->receipt_by}}" placeholder="Receipt By Name" readonly>
        </div>
      </div>
    </div>
    @endif
  </div>
@stop