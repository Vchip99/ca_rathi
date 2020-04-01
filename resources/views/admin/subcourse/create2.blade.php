@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Sub Course Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-university"></i> CRUD Formation </li>
      <li class="active"> Sub Course Formation </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($subCourse->id))
    <form action="{{url('admin/update-subcourse')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="subcourse_id" id="subcourse_id" value="{{$subCourse->id}}">
  @else
   <form action="{{url('admin/create-subcourse')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        @if(isset($subCourse->id))
          @if(count($courses) > 0)
            @foreach($courses as $course)
              @if(isset($subCourse->id) && $subCourse->course_id == $course->id)
                <input id="course" type="text" class="form-control" name="course_text" value="{{$course->name}}" readonly>
                <input type="hidden" name="course" value="{{$course->id}}" >
              @endif
            @endforeach
          @endif
        @else
          <select id="course" class="form-control" name="course" required title="Course" onChange="toggleReceiptBy(this)">
            <option value="">Select Course</option>
            @if(count($courses) > 0)
              @foreach($courses as $course)
                @if(1 != $course->id)
                  <option value="{{$course->id}}">{{$course->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
        @endif
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Course Name:</label>
      <div class="col-sm-3">
        @if(1 == $subCourse->id && 1 == $subCourse->course_id)
          <input id="name" class="form-control" name="name" value="{{ (old('name'))?:$subCourse->name}}" required title="Sub Course" readonly>
        @else
          <input id="name" class="form-control" name="name" value="{{ (old('name'))?:$subCourse->name}}" required title="Sub Course">
        @endif
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('fee')) has-error @endif">
      <label for="fee" class="col-sm-2 col-form-label">Fee:</label>
      <div class="col-sm-3">
        @if(isset($subCourse->id))
          <input type="text" class="form-control" name="fee" id="fee" value="{{$subCourse->fee}}" required="true">
        @else
          <input type="text" class="form-control" name="fee" id="fee" value="" placeholder="Fee" required="true">
        @endif
        @if($errors->has('fee')) <p class="help-block">{{ $errors->first('fee') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('gst')) has-error @endif">
      <label for="gst" class="col-sm-2 col-form-label">GST:</label>
      <div class="col-sm-3">
        @if(isset($subCourse->id))
          <input type="text" class="form-control" name="gst" id="gst" value="{{$subCourse->gst}}" required="true">
        @else
          <input type="text" class="form-control" name="gst" id="gst" value="0" placeholder="GST" required="true">
        @endif
        @if($errors->has('gst')) <p class="help-block">{{ $errors->first('gst') }}</p> @endif
      </div>
    </div>
    <div class="form-group row ">
      <label for="gst" class="col-sm-2 col-form-label">GSTIN:</label>
      <div class="col-sm-3">
        @if(isset($subCourse->id))
          <input type="text" class="form-control" name="gstin" id="gstin" value="{{$subCourse->gstin}}" >
        @else
          <input type="text" class="form-control" name="gstin" id="gstin" value="" placeholder="GSTIN" >
        @endif
      </div>
    </div>
    <div class="form-group row ">
      <label for="gst" class="col-sm-2 col-form-label">CIN:</label>
      <div class="col-sm-3">
        @if(isset($subCourse->id))
          <input type="text" class="form-control" name="cin" id="cin" value="{{$subCourse->cin}}" >
        @else
          <input type="text" class="form-control" name="cin" id="cin" value="" placeholder="CIN" >
        @endif
      </div>
    </div>
    <div class="form-group row ">
      <label for="gst" class="col-sm-2 col-form-label">PAN:</label>
      <div class="col-sm-3">
        @if(isset($subCourse->id))
          <input type="text" class="form-control" name="pan" id="pan" value="{{$subCourse->pan}}">
        @else
          <input type="text" class="form-control" name="pan" id="pan" value="" placeholder="PAN">
        @endif
      </div>
    </div>
    @if(isset($subCourse->id) && 1 == $subCourse->id && 1 == $subCourse->course_id)
      <div class="form-group row hide" id="receiptByDiv">
    @else
      <div class="form-group row @if ($errors->has('receipt_by')) has-error @endif" id="receiptByDiv">
    @endif
      <label for="course" class="col-sm-2 col-form-label">Receipt By Name:</label>
      <div class="col-sm-3">
        @if(isset($subCourse->id))
          <input type="text" class="form-control" name="receipt_by" value="{{$subCourse->receipt_by}}">
        @else
          <input type="text" class="form-control" name="receipt_by" value="" placeholder="Receipt By Name">
        @endif
        @if($errors->has('receipt_by')) <p class="help-block">{{ $errors->first('receipt_by') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
          <button id="submitBtn" type="submit" class="btn btn-primary" title="Submit">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  $('#course').focus();

  function toggleReceiptBy(ele){
    var course = $(ele).val();
    if(1 == course){
      document.getElementById('receiptByDiv').classList.add('hide');
    } else {
      document.getElementById('receiptByDiv').classList.remove('hide');
    }
  }
</script>
@stop