@extends('admin.master')
@section('module_title')
   <link href="{{ asset('css/datepicker.css?ver=1.0')}}" rel="stylesheet"/>
   <script src="{{ asset('js/jquery-ui.js?ver=1.0')}}"></script>
   <script src="{{ asset('js/bootstrap-datepicker.js?ver=1.0')}}"></script>
  <section class="content-header">
    <h1> Admission Receipt </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> Admission Receipt </li>
    </ol>
  </section>
  <style type="text/css">
    .ui-menu {
      list-style: none;
      padding: 2px;
      margin: 0;
      display: block;
      outline: none;
    }
    .ui-menu-item {
      list-style: none;
      padding: 2px;
      margin: 0;
      display: block;
      font-size: 20px;
      background-color: white;
      width: 260px;
    }
  </style>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  @if(count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
    <div class="form-group row">
      <div class="col-sm-2">&nbsp;</div>
      <div class="col-sm-2">
        <label><input type="radio" name="admission_type" value="new" checked="true" onChange="toggleAdmissionType(this);"> New Admission </label>
      </div>
      <div class="col-sm-2">
        <label><input type="radio" name="admission_type" value="existing" onChange="toggleAdmissionType(this);"> Existing Admission</label>
      </div>
    </div>
    <div class="form-group row hide" id="existingDiv">
      <label for="fee" class="col-sm-2 col-form-label">Search Existing User</label>
      <div class="col-sm-3 ui-widget">
        <input type="text" id="search_user" name="user_id" class="form-control" placeholder="enter user id or name" autocomplete="off">
      </div>
    </div>
   <form action="{{url('admin/create-admission-receipt')}}" method="POST" id="submitForm">
    {{ csrf_field() }}
    <div class="form-group row">
      <label for="user_id" class="col-sm-2 col-form-label">User Id:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="user_id" id="user_id" value="{{$nextUserId}}" placeholder="user id" required="true" readonly>
      </div>
      <div class="col-sm-3">
          <input type="text" id="payment_date" class="form-control" name="payment_date" value="{{ date('d-m-Y') }}" placeholder="DD-MM-YYYY">
      </div>
    </div>
    <div class="form-group row">
      <label for="fee" class="col-sm-2 col-form-label">Name:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="f_name" id="f_name" value="{{(old('f_name'))?:NULL}}" placeholder="first name" required="true">
      </div>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="m_name" id="m_name" value="{{(old('m_name'))?:NULL}}" placeholder="middle name" required="true">
      </div>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="l_name" id="l_name" value="{{(old('l_name'))?:NULL}}" placeholder="last name" required="true">
      </div>
    </div>
    <div class="form-group row @if ($errors->has('phone')) has-error @endif">
      <label for="phone" class="col-sm-2 col-form-label">Phone:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="phone" id="phone" value="{{(old('phone'))?:NULL}}" pattern="[0-9]{10}" placeholder="Enter 10 digits mobile number"  required="true">
        @if($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        <select id="course" class="form-control" name="course" onChange="selectSubCourse(this);" required >
            <option value="">Select Course</option>
            @if(count($courses) > 0)
              @foreach($courses as $course)
                  <option value="{{$course->id}}">{{$course->name}}</option>
              @endforeach
            @endif
        </select>
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('sub_course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Course Name:</label>
      <div class="col-sm-3">
        <select id="subcourse" class="form-control" name="sub_course" required onChange="selectBatch(this);">
          <option value="">Select Sub Course</option>
        </select>
        @if($errors->has('subcourse')) <p class="help-block">{{ $errors->first('subcourse') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label">Batch Name:</label>
      <div class="col-sm-3">
        <select id="batch" class="form-control" name="batch" required onChange="checkPayments(this);">
          <option value="">Select Batch</option>
        </select>
        @if($errors->has('batch')) <p class="help-block">{{ $errors->first('batch') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('fee')) has-error @endif">
      <label for="fee" class="col-sm-2 col-form-label">Fee:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="fee" id="fee" value="0" placeholder="Fee" required="true" readonly>
        @if($errors->has('fee')) <p class="help-block">{{ $errors->first('fee') }}</p> @endif
      </div>
    </div>
    <div id="gstDiv">
      <div class="form-group row @if ($errors->has('gst')) has-error @endif">
        <label for="gst" class="col-sm-2 col-form-label">Gst:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="gst" id="gst" value="0" placeholder="Gst"  readonly>
          @if($errors->has('gst')) <p class="help-block">{{ $errors->first('gst') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <label for="fee_type" class="col-sm-2 col-form-label">Type:</label>
        <div class="col-sm-3">
          <input type="checkbox" name="fee_type" id="fee_type" checked="true" onChange="toggleFeeType(this);">
          <input type="hidden" name="fee_type_text" id="fee_type_text" value="">
        </div>
      </div>
    </div>
    <div class="form-group row">
      <label for="fee_method" class="col-sm-2 col-form-label">&nbsp;</label>
      <div class="col-sm-2">
        <label><input type="radio" name="fee_method" value="1" checked="true" onChange="togglePaymentMethod(this);"> Cash </label>
      </div>
      <div class="col-sm-2">
        <label><input type="radio" name="fee_method" value="0" onChange="togglePaymentMethod(this);"> Cheque </label>
      </div>
    </div>
    <div class="form-group row hide" id="chequeDiv">
      <label for="cheque_no" class="col-sm-2 col-form-label">Cheque No:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="cheque_no" id="cheque_no" value="" placeholder="Cheque No">
        @if($errors->has('cheque_no')) <p class="help-block">{{ $errors->first('cheque_no') }}</p> @endif
      </div>
    </div>
    <div class="form-group row hide" id="showNote">
      <label class="col-sm-12 col-form-label"><b style="color: blue;" id="note"></b></label>
    </div>
    <div class="form-group row hide" id="showDiscountNote">
      <label class="col-sm-12 col-form-label"><b style="color: blue;" id="discountNote"></b></label>
    </div>
    <div class="form-group row hide" id="showRefundNote">
      <label class="col-sm-12 col-form-label"><b style="color: blue;" id="refundNote"></b></label>
    </div>
    <div class="form-group row @if ($errors->has('payment_type')) has-error @endif">
      <label for="payment_type" class="col-sm-2 col-form-label">Payment Type:</label>
      <div class="col-sm-2">
        <label><input type="radio" name="payment_type" value="1" checked="true" onChange="toggleRemainderDate(this);"> Full Payment</label>
      </div>
      <div class="col-sm-2">
        <label><input type="radio" name="payment_type" value="0" onChange="toggleRemainderDate(this);"> Partial Payment</label>
      </div>
      @if($errors->has('payment_type')) <p class="help-block">{{ $errors->first('payment_type') }}</p> @endif
    </div>
    <div class="form-group row @if ($errors->has('amount')) has-error @endif">
      <label for="amount" class="col-sm-2 col-form-label">Amount Rs:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" id="amount" name="amount" value="{{(old('amount'))?:NULL}}" placeholder="Amount" required>
        @if($errors->has('amount')) <p class="help-block">{{ $errors->first('amount') }}</p> @endif
      </div>
      <div class="col-sm-6">
          <textarea class="form-control" name="comment" placeholder="enter comment"></textarea>
      </div>
    </div>
    <div id="remainderDiv" class="form-group row hide">
      <label for="remainder_date" class="col-sm-2 col-form-label">Remainder Date:</label>
      <div class="col-sm-3">
          <input type="text" id="remainder_date" class="form-control" name="remainder_date" value="" placeholder="DD-MM-YYYY">
      </div>
    </div>
    <div id="singleReceipt">
      <div class="form-group row @if ($errors->has('receipt_by')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Receipt By Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" id="receipt_by" name="receipt_by" value="" placeholder="Receipt By Name" readonly>
          @if($errors->has('receipt_by')) <p class="help-block">{{ $errors->first('receipt_by') }}</p> @endif
        </div>
      </div>
    </div>
    <div id="multipleReceipt" class="hide">
      <div class="form-group row ">
        <label for="course" class="col-sm-2 col-form-label"></label>
        <div class="col-sm-3">
          <input type="button" class="btn btn-primary" value="Equally Distribute Amount" onClick="divideAmount();">
        </div>
      </div>
      <div class="form-group row ">
        <label for="course" class="col-sm-2 col-form-label">Receipt By Name:</label>
        <div class="col-sm-3"></div>
      </div>
      <div class="form-group row ">
        <label for="course" class="col-sm-2 col-form-label">Ram Rathi:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="ram_rathi" name="ram_rathi" value="" placeholder="Amount">
        </div>
        <div class="col-sm-6">
          <b style="color: blue;" id="ram_rathi_note"></b>
        </div>
      </div>
      <div class="form-group row ">
        <label for="course" class="col-sm-2 col-form-label">Shyam Rathi:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="shyam_rathi" name="shyam_rathi" value="" placeholder="Amount">
        </div>
        <div class="col-sm-6">
          <b style="color: blue;" id="shyam_rathi_note"></b>
        </div>
      </div>
      <div class="form-group row ">
        <label for="course" class="col-sm-2 col-form-label">Giridhar Rathi:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="giridhar_rathi" name="giridhar_rathi" value="" placeholder="Amount">
        </div>
        <div class="col-sm-6">
          <b style="color: blue;" id="giridhar_rathi_note"></b>
        </div>
      </div>
      <div class="form-group row ">
        <label for="course" class="col-sm-2 col-form-label">Dipti Rathi:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="dipti_rathi" name="dipti_rathi" value="" placeholder="Amount">
        </div>
        <div class="col-sm-6">
          <b style="color: blue;" id="dipti_rathi_note"></b>
        </div>
      </div>
      <div class="form-group row ">
        <label for="course" class="col-sm-2 col-form-label">Sunita Rathi:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="sunita_rathi" name="sunita_rathi" value="" placeholder="Amount">
        </div>
        <div class="col-sm-6">
          <b style="color: blue;" id="sunita_rathi_note"></b>
        </div>
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <input type="hidden" id="paid_amount" name="paid_amount" value="">
        <button id="submitBtn" type="submit" class="btn btn-primary" >Submit</button>
      </div>
    </div>
    </form>
    <input type="hidden" id="users_phone">
  </div>
<script type="text/javascript">
  $( function() {
    $( "#search_user" ).autocomplete({

      source: function( request, response ) {
        $.ajax( {
          url: "{{url('admin/get-user-by-user-id')}}",
          method: "POST",
          data: {
            user: request.term
          },
          success: function( data ) {
            if(data.length){
              var usersPhone = document.getElementById('users_phone');
              usersPhone.value = '';
              response($.map(data, function (item) {
                  if(!usersPhone.value){
                    usersPhone.value = item.id+':'+item.phone;
                  } else {
                    usersPhone.value += ','+item.id+':'+item.phone;
                  }
                  return {
                      label: item.id+':'+item.name,
                      value: item.id+':'+item.name,
                  };
              }));

            } else {
              document.getElementById('f_name').value = '';
              document.getElementById('m_name').value = '';
              document.getElementById('l_name').value = '';
              document.getElementById('user_id').value = '';
              document.getElementById('phone').value = '';
            }
            document.getElementById("course").selectedIndex = "0";
            document.getElementById("subcourse").selectedIndex = "0";
            document.getElementById("batch").selectedIndex = "0";
            document.getElementById("note").innerHTML = '';
            document.getElementById('showNote').classList.add('hide');
            resetNotes();
            $('#fee').val('');
            $('#gst').val('');
          }
        } );
      },
      minLength: 1,
      select: function( event, ui ) {
        var value = ui.item.value;
        if(value){
          var valArr = value.split(':');
          $("#user_id").val(valArr[0]);
          var userName = valArr[1].split(' ');
          $("#f_name").val(userName[0]);
          $("#m_name").val(userName[1]);
          $("#l_name").val(userName[2]);
          var usersPhone = $("#users_phone").val();
          if(usersPhone){
            var phones = usersPhone.split(',');
            if(phones.length){
              $.each(phones, function(id,userPhone){
                var userPhoneArr = userPhone.split(':');
                if(valArr[0] == userPhoneArr[0]){
                  $('#phone').val(userPhoneArr[1]);
                }
              });
            }
          }
          document.getElementById("note").innerHTML = '';
          document.getElementById('showNote').classList.add('hide');
          document.getElementById('f_name').setAttribute('readonly', true);
          document.getElementById('m_name').setAttribute('readonly', true);
          document.getElementById('l_name').setAttribute('readonly', true);
          document.getElementById('user_id').setAttribute('readonly', true);
          document.getElementById('phone').setAttribute('readonly', true);
          resetNotes();
        }
      }
    } );
  } );

  $(function () {
    $("#remainder_date").datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          todayHighlight: true
    });
    $("#payment_date").datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          todayHighlight: true
    });
  });

  function selectName(ele) {
    var value = $(ele).val();
    if(value){
      var valArr = value.split(':');
      $("#user_id").val(valArr[0]);
      var userName = valArr[1].split(' ');
      $("#f_name").val(userName[0]);
      $("#m_name").val(userName[1]);
      $("#l_name").val(userName[2]);
      $("#search_user").val(valArr[0]+':'+userName[0]+' '+userName[2]);
      $("#suggesstion-box").html('');
      $("#suggesstion-box").hide();
    }
  }

  function divideAmount(){
    var amount = document.getElementById('amount').value;
    var dividedAmount = amount/5;
    document.getElementById('ram_rathi').value = dividedAmount;
    document.getElementById('shyam_rathi').value = dividedAmount;
    document.getElementById('giridhar_rathi').value = dividedAmount;
    document.getElementById('dipti_rathi').value = dividedAmount;
    document.getElementById('sunita_rathi').value = dividedAmount;
  }

  function selectSubCourse(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/get-sub-courses-by-id')}}",
          data: {course_id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subcourse');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Course';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    } else {
      select = document.getElementById('subcourse');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Sub Course';
      select.appendChild(opt);
    }
    select = document.getElementById('batch');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select Batch';
    select.appendChild(opt);
  }

  function selectBatch(ele){
    var subCourseId = parseInt($(ele).val());
    var courseId = document.getElementById('course').value;
    if( subCourseId > 0 && courseId > 0 ){
      if( 1 == courseId && 1 == subCourseId){
        document.getElementById('singleReceipt').classList.add('hide');
        document.getElementById('multipleReceipt').classList.remove('hide');
      } else {
        document.getElementById('singleReceipt').classList.remove('hide');
        document.getElementById('multipleReceipt').classList.add('hide');
        // document.getElementById('receipt_by').value = selectedOption.getAttribute('data-receipt_by');
      }
      $.ajax({
          method: "POST",
          url: "{{url('admin/get-batches-by-course-id-by-sub-course-id')}}",
          data: {course_id:courseId,subcourse_id:subCourseId}
      })
      .done(function( msg ) {
        select = document.getElementById('batch');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Batch';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              opt.setAttribute('data-fee', obj.fee);
              opt.setAttribute('data-gst', obj.gst);
              opt.setAttribute('data-receipt_by', obj.receipt_by);
              select.appendChild(opt);
          });
        }
      });
    }
  }

  function checkPayments(ele){
    var selectedOption = ele.options[ele.selectedIndex];
    var feeStr = selectedOption.getAttribute('data-fee');
    var gstStr = selectedOption.getAttribute('data-gst');
    $('#fee').val(parseInt(feeStr));
    $('#gst').val(parseInt(gstStr));
    document.getElementById('receipt_by').value = selectedOption.getAttribute('data-receipt_by');
    document.getElementById('amount').value = parseInt(feeStr) + parseInt(gstStr);
    if(parseInt(selectedOption.getAttribute('data-gst')) > 0){
      $('#gstDiv > .form-group').removeClass('hide');
    } else {
      $('#gstDiv > .form-group').addClass('hide');
    }
    if(gstStr > 0){
      $('#fee_type').prop('checked', true);
    } else {
      $('#fee_type').prop('checked', false);
    }

    var courseId = document.getElementById('course').value;
    var subCourseId = document.getElementById('subcourse').value;
    var batchId = parseInt($(ele).val());
    var userId = document.getElementById('user_id').value;
    var admissionType = $("input[name='admission_type']:checked").val();
    if(courseId > 0 && subCourseId > 0 && batchId > 0){
      $.ajax({
        method:'POST',
        url: "{{url('admin/get-user-payments-by-courseId-by-subcourseId-by-batchId')}}",
        data:{course_id:courseId,subcourse_id:subCourseId,batch_id:batchId,user_id:userId, admission_type:admissionType}
      }).done(function( result ) {
        if('false' == result['user_id_exist']){
          var coursePayments = result['course_payments'];
          document.getElementById('paid_amount').value = 0;
          var feeValue = $('#fee').val();
          var gstValue = $('#gst').val();

          if(coursePayments.length > 0){
            var total = 0;
            var discountTotal = 0;
            var refundTotal = 0;
            var note = 'Total Paid Rs.';
            var discountNote = 'Discount Rs.';
            var refundNote = 'Refund Rs =';
            var noteMessage = '';
            var discountNoteMessage = '';
            var ram_total = 0;
            var ram_note = 'total Paid:';
            var ram_message = '';
            var shyam_total = 0;
            var shyam_note = 'total Paid:';
            var shyam_message = '';
            var giridhar_total = 0;
            var giridhar_note = 'total Paid:';
            var giridhar_message = '';
            var dipti_total = 0;
            var dipti_note = 'total Paid:';
            var dipti_message = '';
            var sunita_total = 0;
            var sunita_note = 'total Paid:';
            var sunita_message = '';
            var selectedFeeType = '';

            $.each(coursePayments, function(idx, obj) {
              if(1 == obj.course_payment_type){
                if(0 == idx){
                  noteMessage = parseInt(obj.amount);
                  total = parseInt(obj.amount);
                } else {
                  noteMessage +=  '+' + parseInt(obj.amount);;
                  total += parseInt(obj.amount);;
                }
              } else if(2 == obj.course_payment_type){
                if(0 == discountTotal){
                  discountNoteMessage = parseInt(obj.amount);
                  discountTotal = parseInt(obj.amount);
                } else {
                  discountNoteMessage +=  '+' + parseInt(obj.amount);;
                  discountTotal += parseInt(obj.amount);;
                }
              } else {
                refundTotal += parseInt(obj.amount);
              }

              if(obj.ram_rathi > 0){
                if(0 == ram_total){
                  ram_total = parseInt(obj.ram_rathi);
                  ram_message = obj.ram_rathi;
                } else {
                  if(1 == obj.course_payment_type){
                    ram_total += parseInt(obj.ram_rathi);
                    ram_message += '+' + obj.ram_rathi;
                  } else if(3 == obj.course_payment_type){
                    ram_total -= parseInt(obj.ram_rathi);
                    ram_message += '-' + obj.ram_rathi;
                  }
                }
              }

              if(obj.shyam_rathi > 0){
                if(0 == shyam_total){
                  shyam_total = parseInt(obj.shyam_rathi);
                  shyam_message = obj.shyam_rathi;
                } else {
                  if(1 == obj.course_payment_type){
                    shyam_total += parseInt(obj.shyam_rathi);
                    shyam_message += '+' + obj.shyam_rathi;
                  } else if(3 == obj.course_payment_type){
                    shyam_total -= parseInt(obj.shyam_rathi);
                    shyam_message += '-' + obj.shyam_rathi;
                  }
                }
              }

              if(obj.giridhar_rathi > 0){
                if(0 == giridhar_total){
                  giridhar_total = parseInt(obj.giridhar_rathi);
                  giridhar_message = obj.giridhar_rathi;
                } else {
                  if(1 == obj.course_payment_type){
                    giridhar_total += parseInt(obj.giridhar_rathi);
                    giridhar_message += '+' + obj.giridhar_rathi;
                  } else if(3 == obj.course_payment_type){
                    giridhar_total -= parseInt(obj.giridhar_rathi);
                    giridhar_message += '-' + obj.giridhar_rathi;
                  }
                }
              }

              if(obj.dipti_rathi > 0){
                if(0 == dipti_total){
                  dipti_total = parseInt(obj.dipti_rathi);
                  dipti_message = obj.dipti_rathi;
                } else {
                  if(1 == obj.course_payment_type){
                    dipti_total += parseInt(obj.dipti_rathi);
                    dipti_message += '+' + obj.dipti_rathi;
                  } else if(3 == obj.course_payment_type){
                    dipti_total -= parseInt(obj.dipti_rathi);
                    dipti_message += '-' + obj.dipti_rathi;
                  }
                }
              }

              if(obj.sunita_rathi > 0){
                if(0 == sunita_total){
                  sunita_total = parseInt(obj.sunita_rathi);
                  sunita_message = obj.sunita_rathi;
                } else {
                  if(1 == obj.course_payment_type){
                    sunita_total += parseInt(obj.sunita_rathi);
                    sunita_message += '+' + obj.sunita_rathi;
                  } else if(3 == obj.course_payment_type){
                    sunita_total -= parseInt(obj.sunita_rathi);
                    sunita_message += '-' + obj.sunita_rathi;
                  }
                }
              }
              if(0 == idx){
                if(1 == obj.fee_type){
                  $('#fee_type').prop('checked', true);
                  $('#fee_type').prop('name', 'fee_type_text');
                  $('#fee_type_text').prop('value', 'on');
                  $('#fee_type_text').prop('name', 'fee_type');
                } else {
                  $('#fee_type').prop('checked', false);
                  $('#fee_type').prop('name', 'fee_type_text');
                  $('#fee_type_text').prop('value', '');
                  $('#fee_type_text').prop('name', 'fee_type');
                }
                $('#fee_type').prop('disabled', true);
                selectedFeeType = obj.fee_type;

                if(1 == obj.payment_type){
                  $('input:radio[name="payment_type"][value="1"]').attr('checked', true);
                  $('input:radio[name="payment_type"][value="0"]').attr('checked', false);
                  $('input:radio[name="payment_type"][value="1"]').attr('disabled', false);
                  $('input:radio[name="payment_type"][value="0"]').attr('disabled', true);
                  document.getElementById('remainderDiv').classList.add('hide');
                } else {
                  $('input:radio[name="payment_type"][value="1"]').attr('checked', false);
                  $('input:radio[name="payment_type"][value="0"]').attr('checked', true);
                  $('input:radio[name="payment_type"][value="1"]').attr('disabled', true);
                  $('input:radio[name="payment_type"][value="0"]').attr('disabled', false);
                  document.getElementById('remainderDiv').classList.remove('hide');
                }
                document.getElementById('amount').value = 0;
                $('#amount').attr('readonly', false);
              }
            });
            note += total +' = '+noteMessage;
            discountNote += discountTotal +' = '+discountNoteMessage;
            document.getElementById('showNote').classList.remove('hide');
            document.getElementById('note').innerHTML = note;
            if(discountTotal > 0){
              document.getElementById('showDiscountNote').classList.remove('hide');
              document.getElementById('discountNote').innerHTML = discountNote;
            }
            if(refundTotal > 0){
              document.getElementById('showRefundNote').classList.remove('hide');
              document.getElementById('refundNote').innerHTML = refundNote+refundTotal;
            }
            if(1 == selectedFeeType){
              if((total + discountTotal) == (parseInt(feeValue) + parseInt(gstValue))){
                $('#submitBtn').attr('disabled', true);
                $.alert({
                   title: 'Alert!',
                    content: 'already total amount is paid. please select another sub course.',
                });
              } else {
                if(refundTotal > 0){
                  $('#submitBtn').attr('disabled', true);
                } else {
                  $('#submitBtn').attr('disabled', false);
                }
              }
            } else {
              if((total + discountTotal) == parseInt(feeValue)){
                $('#submitBtn').attr('disabled', true);
                $.alert({
                   title: 'Alert!',
                    content: 'already total amount is paid. please select another sub course.',
                });
              } else {
                if(refundTotal > 0){
                  $('#submitBtn').attr('disabled', true);
                } else {
                  $('#submitBtn').attr('disabled', false);
                }
              }
            }

            document.getElementById('paid_amount').value = (total + discountTotal);
            if(ram_total > 0){
              document.getElementById('ram_rathi_note').innerHTML = ram_note + ram_total +' = '+ ram_message;
            } else {
              document.getElementById('ram_rathi_note').innerHTML = '';
            }
            if(shyam_total > 0){
              document.getElementById('shyam_rathi_note').innerHTML = shyam_note + shyam_total +' = '+ shyam_message;
            } else {
              document.getElementById('shyam_rathi_note').innerHTML = '';
            }
            if(giridhar_total > 0){
              document.getElementById('giridhar_rathi_note').innerHTML = giridhar_note + giridhar_total +' = '+ giridhar_message;
            } else {
              document.getElementById('giridhar_rathi_note').innerHTML = '';
            }
            if(dipti_total > 0){
              document.getElementById('dipti_rathi_note').innerHTML = dipti_note + dipti_total +' = '+ dipti_message;
            } else {
              document.getElementById('dipti_rathi_note').innerHTML = '';
            }
            if(sunita_total > 0){
              document.getElementById('sunita_rathi_note').innerHTML = sunita_note + sunita_total +' = '+ sunita_message;
            } else {
              document.getElementById('sunita_rathi_note').innerHTML = '';
            }
          } else {
            if(gstValue > 0){
              $('#fee_type').attr('checked', true);
            } else {
              $('#fee_type').attr('checked', false);
            }

            $('input:radio[name="payment_type"][value="1"]').attr('checked', true);
            $('input:radio[name="payment_type"][value="0"]').attr('checked', false);
            $('input:radio[name="payment_type"][value="1"]').attr('disabled', false);
            $('input:radio[name="payment_type"][value="0"]').attr('disabled', false);

            document.getElementById('remainderDiv').classList.add('hide');
            document.getElementById('showNote').classList.add('hide');
            document.getElementById('note').innerHTML = '';
            resetNotes();
            $('#fee_type').attr('disabled', false);
            $('#submitBtn').attr('disabled', false);
          }
        } else {
          $.alert({
             title: 'Alert!',
              content: 'User Id is already existing.please enter new user id.',
          });
        }
      });
    }
  }

  function toggleRemainderDate(ele){
    var paymentType = $(ele).val();
    if(1 == paymentType){
      document.getElementById('remainderDiv').classList.add('hide');
      var feeValue = $('#fee').val();
      var gstValue = $('#gst').val();
      if(true == $('#fee_type').prop('checked')){
        document.getElementById('amount').value = parseInt(feeValue) + parseInt(gstValue);
      } else {
        document.getElementById('amount').value =  parseInt(feeValue);
      }
    } else {
      document.getElementById('remainderDiv').classList.remove('hide');
      document.getElementById('amount').value = 0;
    }
    resetReceiptByValues();
  }

  function toggleFeeType(ele){
    var feeValue = $('#fee').val();
    var gstValue = $('#gst').val();
    if(true == $(ele).prop('checked')){
      document.getElementById('amount').value = parseInt(feeValue) + parseInt(gstValue);
    } else {
      document.getElementById('amount').value =  parseInt(feeValue);
    }
    resetReceiptByValues();
  }

  function togglePaymentMethod(ele){
    var paymentMethod = $(ele).val();
    if(0 == paymentMethod){
      $('#chequeDiv').removeClass('hide');
    } else {
      $('#chequeDiv').addClass('hide');
    }
  }

  function toggleAdmissionType(ele){
    var admissionType = $(ele).val();
    if('existing' == admissionType){
      document.getElementById('existingDiv').classList.remove('hide');
    } else {
      document.getElementById('existingDiv').classList.add('hide');
      document.location.reload();
    }
  }

  function resetNotes(){
    document.getElementById('ram_rathi_note').innerHTML = '';
    document.getElementById('shyam_rathi_note').innerHTML = '';
    document.getElementById('giridhar_rathi_note').innerHTML = '';
    document.getElementById('dipti_rathi_note').innerHTML = '';
    document.getElementById('sunita_rathi_note').innerHTML = '';
    if(document.getElementById('discountNote')){
      document.getElementById('discountNote').innerHTML = '';
    }
    if(document.getElementById('refundNote')){
      document.getElementById('refundNote').innerHTML = '';
    }
  }

  function resetReceiptByValues(){
    document.getElementById('ram_rathi').value = '';
    document.getElementById('shyam_rathi').value = '';
    document.getElementById('giridhar_rathi').value = '';
    document.getElementById('dipti_rathi').value = '';
    document.getElementById('sunita_rathi').value = '';
  }
</script>
@stop