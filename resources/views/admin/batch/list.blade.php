@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1>Batch Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-university"></i> CRUD Formation </li>
      <li class="active">Batch Formation </li>
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
        <a id="" href="{{url('admin/create-batch')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Batch">Add Batch</a>&nbsp;&nbsp;
      </div>
    </div>
  <div style="overflow: auto;">
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Batch</th>
          <th>Sub Course</th>
          <th>Course</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        @if(count($batches) > 0)
          @foreach($batches as $index => $batch)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$batch->name}}</td>
            <td>{{$batch->subcourse->name}}</td>
            <td>{{$batch->course->name}}</td>
            <td>
              <a href="{{url('admin/batch')}}/{{$batch->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30'/>
                </a>
            </td>
            <td>
              <a id="{{$batch->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30'/>
              </a>
              <form id="deleteBatch_{{$batch->id}}" action="{{url('admin/delete-batch')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="batch_id" value="{{$batch->id}}">
              </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="6">No batch is created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this batch',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteBatch_'+id;
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