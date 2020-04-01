@extends('admin.master')
@section('module_title')
   <script src="{{ asset('js/jquery-ui.js?ver=1.0')}}"></script>
   <script src="{{ asset('js/bootstrap-datepicker.js?ver=1.0')}}"></script>
  <section class="content-header">
    <h1> User Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> User Courses </li>
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
  <style type="text/css">
    .table > thead > tr > th,.table > tbody > tr > td,.table > tbody > tr > th {
        border-bottom: 2px solid black !important;
    }
  </style>
@stop
@section('admin_content')
  <div class="container">
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
    <div class="form-group row admin_div">
      <label for="fee" class="col-sm-2 col-form-label">Search User</label>
      <div class="col-sm-3 ui-widget">
        <input type="text" id="search_user" name="user_id" class="form-control" placeholder="enter user id or name" autocomplete="off">
      </div>
    </div>
    <div class="form-group row">
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
              <th>Generated By</th>
              <th>Comment</th>
              <th>Pdf</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody id="tbody">
          </tbody>
        </table>
      </div>
    </div>

<script type="text/javascript">
  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'You want to delete this payment?',
      type: 'red',
      typeAnimated: true,
      buttons: {
        Ok: {
            text: 'Ok',
            btnClass: 'btn-red',
            action: function(){
              var id = $(ele).attr('id');
              formId = 'deleteCoursePayment_'+id;
              document.getElementById(formId).submit();
            }
        },
        Cancle: function () {
        }
      }
    });
  }

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
              // var usersPhone = document.getElementById('users_phone');
              // usersPhone.value = '';
              response($.map(data, function (item) {
                  // if(!usersPhone.value){
                  //   usersPhone.value = item.id+':'+item.phone;
                  // } else {
                  //   usersPhone.value += ','+item.id+':'+item.phone;
                  // }
                  return {
                      label: item.id+':'+item.name,
                      value: item.id+':'+item.name,
                  };
              }));

            }
          }
        } );
      },
      minLength: 1,
      select: function( event, ui ) {
        var value = ui.item.value;
        if(value){
          var valArr = value.split(':');
          $("#user_id").val(valArr[0]);
          console.log(valArr);
          userId = valArr[0];
          if(userId > 0){
            $.ajax({
              method: "POST",
              url: "{{url('admin/get-user-course-payments')}}",
              data: {user_id:userId}
            })
            .done(function( result ) {
              renderResult(result);
            });
          }

        }
      }
    } );
  } );

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
          var editUrl = "{{url('admin/course-payment')}}/"+obj.id+'/edit';
          if(1 == obj.course_payment_type){
            eleUserId.innerHTML = '<a href="'+editUrl+'">'+obj.user_id+'-'+obj.name+'</a>' ;
          } else {
            eleUserId.innerHTML = obj.user_id+'-'+obj.name;
          }
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

          var eleAdmin = document.createElement('td');
          eleAdmin.innerHTML = obj.generated_by;
          eleTr.appendChild(eleAdmin);

          var eleComment = document.createElement('td');
          eleComment.innerHTML = obj.comment;
          eleTr.appendChild(eleComment);

          var elePdf = document.createElement('td');
          var pdfUrl = "{{url('admin/show-student-receipt')}}/"+obj.id;
          elePdf.innerHTML = '<a href="'+pdfUrl+'" target="_blank">view pdf</a>';
          eleTr.appendChild(elePdf);

          var eleDelete = document.createElement('td');
          var deleteUrl = "{{url('admin/delete-course-payment')}}";
          var imageUrl  = "{{asset('images/delete2.png')}}";
          var csrfField = '{{ csrf_field() }}';
          var methodField = '{{ method_field('DELETE') }}';
          eleDelete.innerHTML = '<a id="'+obj.id+'" onclick="confirmDelete(this);"><img src="'+imageUrl+'" width="30" height="30" title="Delete" /></a><form id="deleteCoursePayment_'+obj.id+'" action="'+deleteUrl+'" method="POST" style="display: none;">'+csrfField+''+methodField+'<input type="hidden" name="course_payment_id" value="'+obj.id+'"></form>';
          eleTr.appendChild(eleDelete);
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
      eleStatus.setAttribute('colspan', '5');
      eleTr.appendChild(eleStatus);
      body.appendChild(eleTr);
    } else {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'No Result';
      eleIndex.setAttribute('colspan', '15');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

</script>
@stop
