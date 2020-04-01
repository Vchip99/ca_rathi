@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Course Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-university"></i> CRUD Formation </li>
      <li class="active"> Course Formation </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($course->id))
    <form action="{{url('admin/update-course')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="course_id" id="course_id" value="{{$course->id}}">
  @else
   <form action="{{url('admin/create-course')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label for="name" class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        @if(isset($course->id))
          <input type="text" class="form-control" name="name" id="name" value="{{$course->name}}" required="true" @if(1 == $course->id) readonly @endif>
        @else
          <input type="text" class="form-control" name="name" id="name" value="{{ (old('name'))?:''}}" placeholder="Course Name" required="true">
        @endif
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
        @if(1 == $course->id)
          <a class="btn btn-primary" href="{{url()->previous()}}" title="Back">Back</a>
        @else
          <button id="submitBtn" type="submit" class="btn btn-primary" title="Submit">Submit</button>
        @endif
      </div>
    </div>
    </form>
  </div>
  <script type="text/javascript">
    $('#name').focus();
  </script>
@stop