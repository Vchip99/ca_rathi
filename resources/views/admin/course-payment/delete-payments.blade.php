@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Delete Payments </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Delete </li>
      <li class="active"> Delete Payments </li>
    </ol>
  </section>
  <style type="text/css">
    .table > thead > tr > th,.table > tbody > tr > td,.table > tbody > tr > th {
        border-bottom: 2px solid black !important;
    }
  </style>
@stop
@section('admin_content')
  &nbsp;
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <form id="deletePayments" action="{{url('admin/delete-payments')}}" method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <div class="col-sm-3">
          <select class="form-control" id="course" name="course" title="Course" onChange="selectSubCourse(this);">
            <option value="">Select Course</option>
            @if(count($courses) > 0)
              @foreach($courses as $course)
                  <option value="{{$course->id}}">{{$course->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="col-sm-3">
          <select class="form-control" id="subcourse" name="subcourse" title="Sub Course" onChange="selectBatch(this);">
            <option value="">Select Sub Course</option>
          </select>
        </div>
        <div class="col-sm-3">
          <select class="form-control" id="batch" name="batch" title="Batch" onChange="showPayments();">
            <option value="">Select Batch</option>
          </select>
        </div>
        <div class="col-sm-2">
          <input type="button" id="delete" name="delete" class="form-control btn btn-primary" value="Delete" onClick="confirmDelete();">
        </div>
      </form>
    </div>
    <div style="overflow: auto;">
      <table class="table" border="1">
        <thead class="">
          <tr>
            <th>#</th>
            <th>UserId</th>
            <th>Course</th>
            <th>Sub Course</th>
            <th>Batch</th>
            <th>Admission</th>
            <th>Refund</th>
            <th>CGST</th>
            <th>SGST</th>
            <th>Total</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="tbody">
        </tbody>
      </table>
    </div>
</div>
<script type="text/javascript">

  function confirmDelete(){
    var courseId = document.getElementById('course').value;
    var subcourseId = document.getElementById('subcourse').value;
    var batchId = document.getElementById('batch').value;
    if(!courseId){
      $.alert({
      title: 'Alert',
      content: 'Please select course.',
      });
      return false;
    }
    if(!subcourseId){
      $.alert({
      title: 'Alert',
      content: 'Please select sub course.',
      });
      return false;
    }
    if(!batchId){
      $.alert({
      title: 'Alert',
      content: 'Please select batch.',
      });
      return false;
    }
    if(courseId && subcourseId && batchId){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete all payment for selected batch?',
        type: 'red',
        typeAnimated: true,
        buttons: {
          Ok: {
              text: 'Ok',
              btnClass: 'btn-red',
              action: function(){
                document.getElementById('deletePayments').submit();
              }
          },
          Cancle: function () {
          }
        }
      });
      return;
    }
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
    defaultBatch()
  }

  function selectBatch(ele){
    var subCourseId = parseInt($(ele).val());
    var courseId = document.getElementById('course').value;
    if( subCourseId > 0 && courseId > 0 ){
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
              select.appendChild(opt);
          });
        }
      });
    } else {
      defaultBatch()
    }
  }

  function defaultBatch(){
    select = document.getElementById('batch');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select Batch';
    select.appendChild(opt);
    var allOpt = document.createElement('option');
    allOpt.value = 'All';
    allOpt.innerHTML = 'All';
    select.appendChild(allOpt);
  }

  function showPayments(){
    var batchId = document.getElementById('batch').value;
    var courseId = document.getElementById('course').value;
    var subcourseId = document.getElementById('subcourse').value;
    if(subcourseId && courseId && batchId){
      $.ajax({
        method: "POST",
        url: "{{url('admin/get-user-total-paid-by-course-id-by-sub-course-id-by-batch-id-for-payments')}}",
        data: {course_id:courseId,subcourse_id:subcourseId,batch_id:batchId}
      })
      .done(function( result ) {
        renderResult(result);
      });
    }
  }

  function renderResult(result){
    body = document.getElementById('tbody');
    body.innerHTML = '';
    var index = 1;
    var admissionTotal = 0;
    var refundTotal = 0;
    var cgst = 0;
    var sgst = 0;
    if( 0 < result.length){
      $.each(result, function(idx, obj) {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = index++;
          eleTr.appendChild(eleIndex);

          var eleUserId = document.createElement('td');
          eleUserId.innerHTML = obj.user_id;
          eleTr.appendChild(eleUserId);

          var eleCourse = document.createElement('td');
          eleCourse.innerHTML = obj.course;
          eleTr.appendChild(eleCourse);

          var eleSubCourse = document.createElement('td');
          eleSubCourse.innerHTML = obj.subcourse;
          eleTr.appendChild(eleSubCourse);

          var eleBatch = document.createElement('td');
          eleBatch.innerHTML = obj.batch;
          eleTr.appendChild(eleBatch);

          if(1 == obj.fee_type){
            var calAmount = obj.amount/1.18;
            if(1 == obj.course_payment_type){
              admissionTotal = parseFloat(admissionTotal) + parseFloat(calAmount.toFixed(2));
            } else {
              refundTotal = parseFloat(refundTotal) + parseFloat(calAmount.toFixed(2));
            }

            if(1 == obj.course_payment_type){
              var eleAdmission = document.createElement('td');
              eleAdmission.innerHTML = calAmount.toFixed(2);
              eleTr.appendChild(eleAdmission);

              var eleRefund = document.createElement('td');
              eleRefund.innerHTML = 0;
              eleTr.appendChild(eleRefund);

              var eleCgst = document.createElement('td');
              var calCgst = (obj.amount/1.18)*0.09;
              eleCgst.innerHTML = '+'+calCgst.toFixed(2);
              eleTr.appendChild(eleCgst);
              cgst = parseFloat(cgst) + parseFloat(calCgst.toFixed(2));

              var eleSgst = document.createElement('td');
              var calSgst = (obj.amount/1.18)*0.09;
              eleSgst.innerHTML = '+'+calSgst.toFixed(2);
              eleTr.appendChild(eleSgst);
              sgst = parseFloat(sgst) + parseFloat(calSgst.toFixed(2));
            } else {
              var eleAdmission = document.createElement('td');
              eleAdmission.innerHTML = 0;
              eleTr.appendChild(eleAdmission);

              var eleRefund = document.createElement('td');
              eleRefund.innerHTML = calAmount.toFixed(2);
              eleTr.appendChild(eleRefund);

              var eleCgst = document.createElement('td');
              var calCgst = (obj.amount/1.18)*0.09;
              eleCgst.innerHTML = '-'+calCgst.toFixed(2);
              eleTr.appendChild(eleCgst);
              cgst = parseFloat(cgst) - parseFloat(calCgst.toFixed(2));

              var eleSgst = document.createElement('td');
              var calSgst = (obj.amount/1.18)*0.09;
              eleSgst.innerHTML = '-'+calSgst.toFixed(2);
              eleTr.appendChild(eleSgst);
              sgst = parseFloat(sgst) - parseFloat(calSgst.toFixed(2));
            }
          } else {
            if(1 == obj.course_payment_type){
              var eleAdmission = document.createElement('td');
              eleAdmission.innerHTML = obj.amount;
              eleTr.appendChild(eleAdmission);
              admissionTotal = parseFloat(admissionTotal) + parseFloat(obj.amount);

              var eleRefund = document.createElement('td');
              eleRefund.innerHTML = 0;
              eleTr.appendChild(eleRefund);
            } else {
              var eleAdmission = document.createElement('td');
              eleAdmission.innerHTML = 0;
              eleTr.appendChild(eleAdmission);

              var eleRefund = document.createElement('td');
              eleRefund.innerHTML = obj.amount;
              eleTr.appendChild(eleRefund);
              refundTotal = parseFloat(refundTotal) + parseFloat(obj.amount);
            }
            var eleCgst = document.createElement('td');
            eleCgst.innerHTML = 0;
            eleTr.appendChild(eleCgst);
            cgst = parseFloat(cgst) + 0;

            var eleSgst = document.createElement('td');
            eleSgst.innerHTML = 0;
            eleTr.appendChild(eleSgst);
            sgst = parseFloat(sgst) + 0;
          }
          var eleAmount = document.createElement('td');
          eleAmount.innerHTML = parseFloat(obj.amount);
          eleTr.appendChild(eleAmount);

          var eleDate = document.createElement('td');
          eleDate.innerHTML = obj.created_at;
          eleTr.appendChild(eleDate);

          body.appendChild(eleTr);
      });
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = '';
      eleIndex.setAttribute('colspan', '4');
      eleTr.appendChild(eleIndex);

      var eleTotal = document.createElement('td');
      eleTotal.innerHTML = '<b>Total:</b>';
      eleTr.appendChild(eleTotal);

      var eleAdmissionAmount = document.createElement('td');
      eleAdmissionAmount.innerHTML = '+'+admissionTotal.toFixed(2);
      eleTr.appendChild(eleAdmissionAmount);

      var eleRefundAmount = document.createElement('td');
      eleRefundAmount.innerHTML = '-'+refundTotal.toFixed(2);
      eleTr.appendChild(eleRefundAmount);

      var eleCgst = document.createElement('td');
      eleCgst.innerHTML = '+'+cgst.toFixed(2);
      eleTr.appendChild(eleCgst);

      var eleSgst = document.createElement('td');
      eleSgst.innerHTML = '+'+sgst.toFixed(2);
      eleTr.appendChild(eleSgst);

      var eleAmount = document.createElement('td');
      eleAmount.innerHTML = Math.round(admissionTotal - refundTotal + cgst + sgst);
      eleTr.appendChild(eleAmount);

      var eleStatus = document.createElement('td');
      eleStatus.innerHTML = '';
      eleTr.appendChild(eleStatus);
      body.appendChild(eleTr);
      $('#delete').attr('disabled', false);
    } else {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'No Result';
      eleIndex.setAttribute('colspan', '11');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
      $('#delete').attr('disabled', true);
    }
  }
</script>
@stop