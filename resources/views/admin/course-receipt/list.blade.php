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
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="form-group row">
      <div>
        <a href="{{url('admin/create-subcourse-details')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Sub Course Details">Add Sub Course Details</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Sub Course</th>
          <th>Course</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        @if(count($courseReceipts) > 0)
          @foreach($courseReceipts as $index => $courseReceipt)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$courseReceipt->subCourse->name}}</td>
            <td>{{$courseReceipt->course->name}}</td>
            <td>
              <a href="{{url('admin/course-receipt')}}/{{$courseReceipt->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30'/>
                </a>
            </td>
            <td>
            <a id="{{$courseReceipt->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30'/>
                </a>
                <form id="deleteReceipt_{{$courseReceipt->id}}" action="{{url('admin/delete-subcourse-details')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="course_receipt_id" value="{{$courseReceipt->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No sub course details is created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are You sure, you want to delete this sub course details',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteReceipt_'+id;
                    document.getElementById(formId).submit();
                  }
              },
              Cancle: function () {
              }
          }
        });
    }

</script>
@stop