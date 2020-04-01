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
  @if(isset($admin->id))
    <form action="{{url('admin/update-admin')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="admin_id" id="admin_id" value="{{$admin->id}}">
  @else
   <form action="{{url('admin/create-admin')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label for="name" class="col-sm-2 col-form-label">Admin Name:</label>
      <div class="col-sm-3">
        @if(isset($admin->id))
          <input type="text" class="form-control" name="name" id="name" value="{{$admin->name}}" required="true" @if(1 == $admin->id) readonly @endif>
        @else
          <input type="text" class="form-control" name="name" id="name" value="{{ (old('name'))?:''}}" placeholder="Admin Name" required="true">
        @endif
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label for="email" class="col-sm-2 col-form-label">Email:</label>
      <div class="col-sm-3">
          <input type="email" class="form-control" name="email" id="email" value="{{ (old('email'))?:$admin->email}}" placeholder="Admin Email" required="true">
        @if($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('type')) has-error @endif">
      <label class="col-sm-2 col-form-label">Type:</label>
      <div class="col-sm-3">
        <select id="type" class="form-control" name="type" required title="Type">
          <option value="">Select Type</option>
          @if(count($types) > 0)
            @foreach($types as $typeId => $typeName)
              @if(isset($admin->id) && $typeId == $admin->type)
                <option value="{{$typeId}}" selected>{{$typeName}}</option>
              @else
                <option value="{{$typeId}}">{{$typeName}}</option>
              @endif
            @endforeach
          @endif
        </select>
      </div>
    </div>
    <div class="form-group row">
        <label class="control-label col-sm-2">Password:</label>
        <div class="col-sm-3">
          @if(isset($admin->id))
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="">
          @else
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="" required="true">
          @endif
        </div>
        @if($errors->has('password')) <p class="alert alert-danger">{{ $errors->first('password') }}</p> @endif
    </div>
    @if(isset($admin->id))
      <div class="form-group row hide">
    @else
      <div class="form-group row">
    @endif
      <label class="control-label col-sm-2">Confirm Password:</label>
      <div class="col-sm-3">
        @if(isset($admin->id))
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" value="">
        @else
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" value="" required="true">
        @endif
      </div>
      @if($errors->has('confirm_password')) <p class="alert alert-danger">{{ $errors->first('confirm_password') }}</p> @endif
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
        <button id="submitBtn" type="submit" class="btn btn-primary" title="Submit">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop