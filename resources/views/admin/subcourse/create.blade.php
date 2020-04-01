@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Sub Course Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
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
          <select id="course" class="form-control" name="course" required title="Course" >
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
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
        @if(1 == $subCourse->id && 1 == $subCourse->course_id)
          <a class="btn btn-primary" href="{{url()->previous()}}" title="Back">Back</a>
        @else
          <button id="submitBtn" type="submit" class="btn btn-primary" title="Submit">Submit</button>
        @endif
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  $('#course').focus();
</script>
@stop