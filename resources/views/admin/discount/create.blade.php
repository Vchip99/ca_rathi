@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Discount Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-diamond"></i> Discount </li>
      <li class="active"> Discount Formation </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
   <form action="{{url('admin/create-discount')}}" method="POST" id="submitForm">
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        @if(isset($coursePayment->id))
          @if(count($courses) > 0)
            @foreach($courses as $course)
              @if(isset($coursePayment->id) && $coursePayment->course_id == $course->id)
                <input id="course" type="text" class="form-control" name="course_text" value="{{$course->name}}" readonly>
                <input type="hidden" name="course" value="{{$course->id}}" >
              @endif
            @endforeach
          @endif
        @else
          <select id="course" class="form-control" name="course" required title="Course" onChange="selectSubCourse(this);" >
            <option value="">Select Course</option>
            @if(count($courses) > 0)
              @foreach($courses as $course)
                <option value="{{$course->id}}">{{$course->name}}</option>
              @endforeach
            @endif
          </select>
        @endif
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subcourse')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Course Name:</label>
      <div class="col-sm-3">
        @if(isset($coursePayment->id))
          @if(count($subCourses) > 0)
            @foreach($subCourses as $subCourse)
              @if(isset($coursePayment->id) && $coursePayment->sub_course_id == $subCourse->id)
                <input id="subcourse" type="text" class="form-control" name="subcourse_text" value="{{$subCourse->name}}" readonly>
                <input type="hidden" name="subcourse" value="{{$subCourse->id}}" >
              @endif
            @endforeach
          @endif
        @else
          <select id="subcourse" class="form-control" name="subcourse" required title="Sub Course" onChange="selectBatch(this);">
            <option value="">Select Sub Course</option>
          </select>
        @endif
        @if($errors->has('subcourse')) <p class="help-block">{{ $errors->first('subcourse') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label">Batch Name:</label>
      <div class="col-sm-3">
        @if(isset($coursePayment->id))
          @if(count($batches) > 0)
            @foreach($batches as $batch)
              @if(isset($coursePayment->id) && $coursePayment->batch_id == $batch->id)
                <input id="batch" type="text" class="form-control" name="batch_text" value="{{$batch->name}}" readonly>
                <input type="hidden" name="batch" value="{{$batch->id}}" >
              @endif
            @endforeach
          @endif
        @else
          <select id="batch" class="form-control" name="batch" required title="Batch" onChange="selectBatchUser(this);">
            <option value="">Select Batch</option>
          </select>
        @endif
        @if($errors->has('batch')) <p class="help-block">{{ $errors->first('batch') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('user')) has-error @endif">
      <label class="col-sm-2 col-form-label">UserId:</label>
      <div class="col-sm-3">
        @if(isset($coursePayment->id))
          @if(count($users) > 0)
            @foreach($users as $user)
              @if($coursePayment->user_id == $user->id)
              <input id="user" type="text" class="form-control" name="user_text" value="{{$user->f_name}} {{$user->l_name}}" readonly>
              <input type="hidden" name="user" value="{{$user->id}}" >
              @endif
            @endforeach
          @endif
        @else
          <select id="user" class="form-control" name="user" required title="user">
            <option value="">Select User</option>
          </select>
        @endif
        @if($errors->has('user')) <p class="help-block">{{ $errors->first('user') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('discount')) has-error @endif">
      <label class="col-sm-2 col-form-label">Discount:</label>
      <div class="col-sm-3">
        @if(isset($coursePayment->id))
          <input id="discount" class="form-control" name="discount" value="{{ (old('discount'))?:$coursePayment->amount}}" required title="discount" readonly>
        @else
          <input id="discount" class="form-control" name="discount" value="{{ (old('discount'))?:$coursePayment->amount}}" required title="discount">
        @endif
        @if($errors->has('discount')) <p class="help-block">{{ $errors->first('discount') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('remark')) has-error @endif">
      <label class="col-sm-2 col-form-label">Remark:</label>
      <div class="col-sm-3">
        @if(isset($coursePayment->id))
          <textarea id="remark" class="form-control" name="remark" required title="remark" disabled>{{ (old('remark'))?:$coursePayment->comment}}</textarea>
        @else
          <textarea id="remark" class="form-control" name="remark" required title="remark">{{ (old('remark'))?:''}}</textarea>
        @endif
        @if($errors->has('remark')) <p class="help-block">{{ $errors->first('remark') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
        @if(isset($coursePayment->id))
          <a class="btn btn-primary" href="{{url()->previous()}}" title="Back">Back</a>
        @else
          <button id="submitBtn" type="submit" class="btn btn-primary" title="Submit">Submit</button>
        @endif
      </div>
    </div>
    </form>
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

    select = document.getElementById('user');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
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
    }
    select = document.getElementById('user');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }

  function selectBatchUser(ele){
    var batchId = parseInt($(ele).val());
    var courseId = document.getElementById('course').value;
    var subcourseId = document.getElementById('subcourse').value;
    if( subcourseId > 0 && courseId > 0 && batchId > 0){
      $.ajax({
          method: "POST",
          url: "{{url('admin/get-users-by-course-id-by-sub-course-id-by-batch-id')}}",
          data: {course_id:courseId,subcourse_id:subcourseId,batch_id:batchId}
      })
      .done(function( msg ) {
        select = document.getElementById('user');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select User';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.f_name+' '+obj.l_name;
              select.appendChild(opt);
          });
        }
      });
    }
  }
</script>
@stop
