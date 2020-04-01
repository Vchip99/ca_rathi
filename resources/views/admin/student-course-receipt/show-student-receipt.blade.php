@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Download Receipt </h1>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container">
    <div class="form-group row">
      <div id="addCategoryDiv">
        <a id="addCategory" href="{{url('admin/create-student-admission')}}" type="button" class="btn btn-primary" style="float: right;" title="Student Admission">Student Admission</a>&nbsp;&nbsp;
      </div>
    </div>
    <div >
      {{$pdfOutput}}
    </div>

  </div>
@stop