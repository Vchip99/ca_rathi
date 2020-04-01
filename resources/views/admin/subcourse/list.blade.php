@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1>Sub Course Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-university"></i> CRUD Formation </li>
      <li class="active">Sub Course Formation </li>
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
      <div id="">
        <a id="" href="{{url('admin/create-subcourse')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Sub Course">Add Sub Course</a>&nbsp;&nbsp;
      </div>
    </div>
  <div style="overflow: auto;">
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
        @if(count($subCourses) > 0)
          @foreach($subCourses as $index => $subCourse)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$subCourse->name}}</td>
            <td>{{$subCourse->course->name}}</td>
            <td>
              <a href="{{url('admin/subcourse')}}/{{$subCourse->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30'/>
                </a>
            </td>
            <td>
            @if(1 == $subCourse->id)
              &nbsp;
            @else
              <a id="{{$subCourse->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30'/>
              </a>
              <form id="deleteSubCourse_{{$subCourse->id}}" action="{{url('admin/delete-subcourse')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="subcourse_id" value="{{$subCourse->id}}">
              </form>
            @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No sub course is created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this Sub Course, all associated batches will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteSubCourse_'+id;
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