@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Admin Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Admin </li>
      <li class="active"> Admin Formation </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
    @if(count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
      </div>
    @endif
    <form action="{{url('admin/update-admin-password')}}" method="POST">
    {{method_field('PUT')}}
    {{ csrf_field() }}
    <div class="form-group row">
        <label class="control-label col-sm-2">Admin Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="name" placeholder="Name" value="{{Auth::guard('admin')->user()->name}}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label class="control-label col-sm-2">Old Password:</label>
        <div class="col-sm-3">
            <input type="password" class="form-control" name="old_password" placeholder="Old Password" value="" required>
        </div>
        @if($errors->has('old_password')) <p class="alert alert-danger">{{ $errors->first('old_password') }}</p> @endif
    </div>
    <div class="form-group row">
        <label class="control-label col-sm-2">New Password:</label>
        <div class="col-sm-3">
            <input type="password" class="form-control" id="password" name="new_password" placeholder="Password" value="" required="true">
        </div>
        @if($errors->has('new_password')) <p class="alert alert-danger">{{ $errors->first('new_password') }}</p> @endif
    </div>
    <div class="form-group row">
        <label class="control-label col-sm-2">Confirm Password:</label>
        <div class="col-sm-3">
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" value="" required="true">
        </div>
        @if($errors->has('confirm_password')) <p class="alert alert-danger">{{ $errors->first('confirm_password') }}</p> @endif
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop