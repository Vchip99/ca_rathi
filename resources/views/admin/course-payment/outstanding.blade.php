@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Outstanding </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> Outstanding </li>
    </ol>
  </section>
  <style type="text/css">
    .table > thead > tr > th,.table > tbody > tr > td,.table > tbody > tr > th {
        border-bottom: 2px solid black !important;
    }
  </style>
@stop
@section('admin_content')
  <div class="container">
    <form action="{{url('admin/download-outstandings')}}" id="downloadForm">
    <div class="form-group row">
      <div class="col-sm-3">
        <select class="form-control" id="course_id" name="course_id" title="Course" onChange="selectSubCourse(this);">
          <option value="">Select Course</option>
          <option value="All">All</option>
          @if(count($courses) > 0)
            @foreach($courses as $course)
                <option value="{{$course->id}}">{{$course->name}}</option>
            @endforeach
          @endif
        </select>
      </div>
      <div class="col-sm-3">
        <select class="form-control" id="subcourse_id" name="subcourse_id" title="Sub Course" onChange="selectBatch(this);">
          <option value="">Select Sub Course</option>
          <option value="All">All</option>
        </select>
      </div>
      <div class="col-sm-3">
        <select class="form-control" id="batch_id" name="batch_id" title="Batch" onChange="showOutStandings(this);">
          <option value="">Select Batch</option>
          <option value="All">All</option>
        </select>
      </div>
      <div class="col-sm-3">
        <input type="button" name="Download" class="form-control btn btn-primary" value="Download" onClick="checkDate();">
      </div>
    </div>
    <div class="form-group row">
      <div class="col-sm-3">
        <input type="radio" name="due_status" value="1" checked="true" onclick="toggleOutstanding(this);"> All Dues
        <input type="radio" name="due_status" value="0"  onclick="toggleOutstanding(this);"> Todays Dues
      </div>
    </div>
    </form>
    <div style="overflow: auto;">
      <table class="table" border="1">
        <thead class="">
          <tr>
            <th>#</th>
            <th>UserId</th>
            <th>Phone</th>
            <th>Course</th>
            <th>Sub Course</th>
            <th>Batch</th>
            <th>Total Fee</th>
            <th>Paid</th>
            <th>Discount</th>
            <th>Outstanding</th>
            <th>Comment</th>
            <th>Due Date</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @if(count($usersOutstanding) > 0)
            @foreach($usersOutstanding as $index => $userOutstanding)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{$userOutstanding['user_id']}}-{{$userOutstanding['name']}}</td>
                <td>{{$userOutstanding['phone']}}</td>
                <td>{{($coursesName[$userOutstanding['course']])?:deleted}}</td>
                <td>{{($subcoursesName[$userOutstanding['subcourse']])?:deleted}}</td>
                <td>{{($batchesName[$userOutstanding['batch']])?:deleted}}</td>
                <td>{{$userOutstanding['total_fee']}}</td>
                <td>{{$userOutstanding['paid']}}</td>
                <td>{{$userOutstanding['discount']}}</td>
                <td>{{$userOutstanding['outstanding']}}</td>
                <td>{{$userOutstanding['comment']}}</td>
                <td>{{$userOutstanding['remainder_date']}}</td>
              </tr>
            @endforeach
            <tr>
                <td colspan="7">&nbsp;</td>
                <td colspan="2"><b>Total Outstanding:</b></td>
                <td colspan="3">{{ $totalOutstanding }}</td>
            </tr>
          @else
            <tr>
                <td colspan="12">No outstanding result</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
</div>
<script type="text/javascript">
  function selectSubCourse(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/get-sub-courses-by-id')}}",
          data: {course_id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subcourse_id');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Course';
        select.appendChild(opt);
        var allOpt = document.createElement('option');
        allOpt.value = 'All';
        allOpt.innerHTML = 'All';
        select.appendChild(allOpt);
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
      select = document.getElementById('subcourse_id');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Sub Course';
      select.appendChild(opt);
      var allOpt = document.createElement('option');
      allOpt.value = 'All';
      allOpt.innerHTML = 'All';
      select.appendChild(allOpt);
    }
    defaultBatch()
  }

  function selectBatch(ele){
    var subCourseId = parseInt($(ele).val());
    var courseId = document.getElementById('course_id').value;
    if( subCourseId > 0 && courseId > 0 ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/get-batches-by-course-id-by-sub-course-id')}}",
          data: {course_id:courseId,subcourse_id:subCourseId}
      })
      .done(function( msg ) {
        select = document.getElementById('batch_id');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Batch';
        select.appendChild(opt);
        var allOpt = document.createElement('option');
        allOpt.value = 'All';
        allOpt.innerHTML = 'All';
        select.appendChild(allOpt);
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

  function showOutStandings(){
    var batchId = document.getElementById('batch_id').value;
    var courseId = document.getElementById('course_id').value;
    var subcourseId = document.getElementById('subcourse_id').value;
    var dueStatus = $('input[name=due_status]:checked').val();
    if(courseId && subcourseId && batchId){
      $.ajax({
        method: "POST",
        url: "{{url('admin/get-outstanding-by-course-id-by-sub-course-id-by-batch-id')}}",
        data: {course_id:courseId,subcourse_id:subcourseId,batch_id:batchId,due_status:dueStatus}
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
    var totalOutstanding = 0;
    if( 0 < result.length){
      $.each(result, function(idx, obj) {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = index++;
          eleTr.appendChild(eleIndex);

          var eleUserId = document.createElement('td');
          eleUserId.innerHTML = obj.user_id+'-'+obj.name;
          eleTr.appendChild(eleUserId);

          var elePhone = document.createElement('td');
          elePhone.innerHTML = obj.phone;
          eleTr.appendChild(elePhone);

          var eleCourse = document.createElement('td');
          eleCourse.innerHTML = obj.course;
          eleTr.appendChild(eleCourse);

          var eleSubCourse = document.createElement('td');
          eleSubCourse.innerHTML = obj.subcourse;
          eleTr.appendChild(eleSubCourse);

          var eleBatch = document.createElement('td');
          eleBatch.innerHTML = obj.batch;
          eleTr.appendChild(eleBatch);

          var eleAmount = document.createElement('td');
          eleAmount.innerHTML = parseFloat(obj.total_fee);
          eleTr.appendChild(eleAmount);

          var elePaid = document.createElement('td');
          elePaid.innerHTML = obj.paid;
          eleTr.appendChild(elePaid);

          var eleDiscount = document.createElement('td');
          eleDiscount.innerHTML = obj.discount;
          eleTr.appendChild(eleDiscount);

          var eleOutstanding = document.createElement('td');
          eleOutstanding.innerHTML = obj.outstanding;
          eleTr.appendChild(eleOutstanding);
          totalOutstanding += obj.outstanding;

          var eleComment = document.createElement('td');
          eleComment.innerHTML = obj.comment;
          eleTr.appendChild(eleComment);

          var eleDueDate = document.createElement('td');
          eleDueDate.innerHTML = obj.remainder_date;
          eleTr.appendChild(eleDueDate);

          body.appendChild(eleTr);
      });
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = '';
      eleIndex.setAttribute('colspan', '7');
      eleTr.appendChild(eleIndex);

      var eleStatus = document.createElement('td');
      eleStatus.innerHTML = '<b>Total Outstanding:</b>';
      eleStatus.setAttribute('colspan', '2');
      eleTr.appendChild(eleStatus);

      var eleTotal = document.createElement('td');
      eleTotal.innerHTML = totalOutstanding;
      eleTotal.setAttribute('colspan', '3');
      eleTr.appendChild(eleTotal);
      body.appendChild(eleTr);
    } else {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'No outstanding result';
      eleIndex.setAttribute('colspan', '12');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function defaultBatch(){
    select = document.getElementById('batch_id');
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

  function toggleOutstanding(ele){
    showOutStandings();
  }

  function checkDate(){
    var batchId = document.getElementById('batch_id').value;
    var courseId = document.getElementById('course_id').value;
    var subcourseId = document.getElementById('subcourse_id').value;
    if(courseId && subcourseId && batchId){
      document.getElementById('downloadForm').submit();
    }
  }
</script>
@stop