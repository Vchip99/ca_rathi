@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1>Admin Formation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Admin </li>
      <li class="active">Admin Formation </li>
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
        <a id="" href="{{url('admin/create-admin')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Admin">Add Admin</a>&nbsp;&nbsp;
      </div>
    </div>
  <div style="overflow: auto;">
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Admin</th>
          <th>Type</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        @if(count($admins) > 0)
          @foreach($admins as $index => $admin)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$admin->name}}</td>
            <td>
              @if(array_key_exists($admin->type,$types))
                {{$types[$admin->type]}}
              @endif
            </td>
            <td>
              <a href="{{url('admin/admin')}}/{{$admin->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30'/>
                </a>
            </td>
            <td>
              <a id="{{$admin->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30'/>
              </a>
              <form id="deleteAdmin_{{$admin->id}}" action="{{url('admin/delete-admin')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="admin_id" value="{{$admin->id}}">
              </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No admin created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this admin?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteAdmin_'+id;
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