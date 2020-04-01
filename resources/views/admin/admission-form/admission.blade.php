@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Admission Form </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> Admission Form </li>
    </ol>
  </section>
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
   <form action="{{url('admin/create-student-admission')}}" method="POST" id="submitForm" enctype="multipart/form-data">
    {{ csrf_field() }}
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
    <div class="form-group row @if ($errors->has('user_id')) has-error @endif">
      <label for="user_id" class="col-sm-2 col-form-label">User Id:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="user_id" id="user_id" value="{{(old('user_id'))?:NULL}}" placeholder="user id" required="true">
        @if($errors->has('user_id')) <p class="help-block">{{ $errors->first('user_id') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('email')) has-error @endif">
      <label for="email" class="col-sm-2 col-form-label">Email Id:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="email" id="email" value="{{(old('email'))?:NULL}}" placeholder="email id" >
        @if($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('phone')) has-error @endif">
      <label for="phone" class="col-sm-2 col-form-label">Phone:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="phone" id="phone" value="{{(old('phone'))?:NULL}}" pattern="[0-9]{10}" placeholder="Enter 10 digits mobile number"  required="true">
        @if($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('photo')) has-error @endif">
      <label for="photo" class="col-sm-2 col-form-label">Photo:</label>
      <div class="col-sm-3">
          <input type="file" class="form-control" name="photo" id="photo" value="" placeholder="Photo">
      </div>
    </div>
    <div class="form-group row @if ($errors->has('address')) has-error @endif">
      <label for="address" class="col-sm-2 col-form-label">Address:</label>
      <div class="col-sm-6">
          <textarea class="form-control" name="address" placeholder="enter address">{{(old('address'))?:NULL}}</textarea>
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button id="submitBtn" type="submit" class="btn btn-primary" >Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop