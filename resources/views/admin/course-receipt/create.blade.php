@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Sub Course Details </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> Sub Course Details </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($courseReceipt->id))
    <form action="{{url('admin/update-course-receipt')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="course_receipt_id" id="course_receipt_id" value="{{$courseReceipt->id}}">
  @else
   <form action="{{url('admin/create-course-receipt')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        <select id="course" class="form-control" name="course" onChange="selectSubCourse(this);" required title="Course" disabled>
            <option value="">Select Course</option>
            @if(count($courses) > 0)
              @foreach($courses as $course)
                @if( isset($courseReceipt->id) && $courseReceipt->course_id == $course->id)
                  <option value="{{$course->id}}" selected="true">{{$course->name}}</option>
                @else
                  <option value="{{$course->id}}">{{$course->name}}</option>
                @endif
              @endforeach
            @endif
        </select>
        <input type="hidden" name="course" value="{{$courseReceipt->course_id}}">
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('sub_course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Course Name:</label>
      <div class="col-sm-3">
        @if(isset($courseReceipt->id) && count($subCourses) > 0)
          <select id="subcourse" class="form-control" name="sub_course" required title="Sub Course" onChange="toggleReceiptBy(this)" disabled>
            <option value="">Select Sub Course</option>
            @foreach($subCourses as $subCourse)
              @if($courseReceipt->sub_course_id == $subCourse->id)
                <option value="{{$subCourse->id}}" selected="true">{{$subCourse->name}}</option>
              @else
                <option value="{{$subCourse->id}}">{{$subCourse->name}}</option>
              @endif
            @endforeach
          </select>
          <input type="hidden" name="sub_course" value="{{$courseReceipt->sub_course_id}}">
        @else
          <select id="subcourse" class="form-control" name="sub_course" required title="Sub Course" onChange="isSubCourseUsed(this);">
            <option value="">Select Sub Course</option>
          </select>
        @endif
        @if($errors->has('subcourse')) <p class="help-block">{{ $errors->first('subcourse') }}</p> @endif
        <span class="hide" id="receiptError" style="color: white;">Given sub course is already exist with selected course.Please select another sub course.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('fee')) has-error @endif">
      <label for="fee" class="col-sm-2 col-form-label">Fee:</label>
      <div class="col-sm-3">
        @if(isset($courseReceipt->id))
          <input type="text" class="form-control" name="fee" id="fee" value="{{$courseReceipt->fee}}" required="true">
        @else
          <input type="text" class="form-control" name="fee" id="fee" value="" placeholder="Fee" required="true">
        @endif
        @if($errors->has('fee')) <p class="help-block">{{ $errors->first('fee') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('gst')) has-error @endif">
      <label for="gst" class="col-sm-2 col-form-label">Gst:</label>
      <div class="col-sm-3">
        @if(isset($courseReceipt->id))
          <input type="text" class="form-control" name="gst" id="gst" value="{{$courseReceipt->gst}}" required="true">
        @else
          <input type="text" class="form-control" name="gst" id="gst" value="0" placeholder="Gst" required="true">
        @endif
        @if($errors->has('gst')) <p class="help-block">{{ $errors->first('gst') }}</p> @endif
      </div>
    </div>
    <div class="form-group row ">
      <label for="gst" class="col-sm-2 col-form-label">GSTIN:</label>
      <div class="col-sm-3">
        @if(isset($courseReceipt->id))
          <input type="text" class="form-control" name="gstin" id="gstin" value="{{$courseReceipt->gstin}}" >
        @else
          <input type="text" class="form-control" name="gstin" id="gstin" value="" placeholder="gstin" >
        @endif
      </div>
    </div>
    <div class="form-group row ">
      <label for="gst" class="col-sm-2 col-form-label">CIN:</label>
      <div class="col-sm-3">
        @if(isset($courseReceipt->id))
          <input type="text" class="form-control" name="cin" id="cin" value="{{$courseReceipt->cin}}" >
        @else
          <input type="text" class="form-control" name="cin" id="cin" value="" placeholder="cin" >
        @endif
      </div>
    </div>
    <div class="form-group row ">
      <label for="gst" class="col-sm-2 col-form-label">PAN:</label>
      <div class="col-sm-3">
        @if(isset($courseReceipt->id))
          <input type="text" class="form-control" name="pan" id="pan" value="{{$courseReceipt->pan}}">
        @else
          <input type="text" class="form-control" name="pan" id="pan" value="" placeholder="pan">
        @endif
      </div>
    </div>
    @if(isset($courseReceipt->id) && 3 == $courseReceipt->course_id && 8 == $courseReceipt->sub_course_id)
      <div class="form-group row hide" id="receiptByDiv">
    @else
      <div class="form-group row @if ($errors->has('receipt_by')) has-error @endif" id="receiptByDiv">
    @endif
      <label for="course" class="col-sm-2 col-form-label">Receipt By Name:</label>
      <div class="col-sm-3">
        @if(isset($courseReceipt->id))
          <input type="text" class="form-control" name="receipt_by" value="{{$courseReceipt->receipt_by}}">
        @else
          <input type="text" class="form-control" name="receipt_by" value="" placeholder="Receipt By Name">
        @endif
        @if($errors->has('receipt_by')) <p class="help-block">{{ $errors->first('receipt_by') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button id="submitBtn" type="submit" class="btn btn-primary" >Submit</button>
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
    }
  }

  function isSubCourseUsed(){
    var course = document.getElementById('course').value;
    var subcourse = document.getElementById('subcourse').value;
    if(document.getElementById('course_receipt_id')){
      var courseReceiptId = document.getElementById('course_receipt_id').value;
    } else {
      var courseReceiptId = 0;
    }
    if(course && subcourse){
      $.ajax({
        method:'POST',
        url: "{{url('admin/is-sub-course-used')}}",
        data:{course:course,subcourse:subcourse,course_receipt_id:courseReceiptId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('receiptError').classList.remove('hide');
          document.getElementById('receiptError').classList.add('has-error');
          document.getElementById('submitBtn').classList.add('hide');
        } else {
          document.getElementById('receiptError').classList.add('hide');
          document.getElementById('receiptError').classList.remove('has-error');
          document.getElementById('submitBtn').classList.remove('hide');
        }
      });
    }
  }
  function toggleReceiptBy(ele){
    var course = document.getElementById('course').value;
    var subcourse = $(ele).val();
    if(3 == course && 8 == subcourse){
      document.getElementById('receiptByDiv').classList.add('hide');
    } else {
      document.getElementById('receiptByDiv').classList.remove('hide');
    }
  }
</script>
@stop