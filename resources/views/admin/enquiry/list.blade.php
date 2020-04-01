@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/datepicker.css?ver=1.0')}}" rel="stylesheet"/>
  <script src="{{ asset('js/bootstrap-datepicker.js?ver=1.0')}}"></script>
  <section class="content-header">
    <h1> Enquiries </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> Enquiries </li>
    </ol>
  </section>
  <style type="text/css">
    @media only screen and (max-width: 767px){
      select,input[type=text]{
        margin-bottom: 5px;
      }
    }
  </style>
@stop
@section('admin_content')
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="form-group row">
      <div id="">
        <a id="" href="{{url('admin/create-enquiry')}}" type="button" class="btn btn-primary" style="float: right;" title="Create Enquiry">Create Enquiry</a>&nbsp;&nbsp;
      </div>
    </div>
  <div style="overflow: auto;">
    @if(2 == $adminUser->type)
      <div class="form-group row">
        <div class="col-sm-3">
          <input type="text" id="from" name="from" class="form-control" placeholder="yyyy-mm-dd">
        </div>
        <div class="col-sm-3">
          <input type="text" id="to" name="to" class="form-control" placeholder="yyyy-mm-dd" >
        </div>
        <div class="col-sm-3">
          <select name="course" class="form-control" onChange="showEnquiry(this);">
            <option value=""> Select Course</option>
            <option value="12th"> 12th</option>
            <option value="CPT"> CPT</option>
            <option value="PICC"> PICC</option>
            <option value="Other"> Other</option>
          </select>
        </div>
      </div>
    @endif
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Student No</th>
          <th>Course</th>
          <th>Edit</th>
          @if(2 == $adminUser->type)
            <th>Delete</th>
          @endif
        </tr>
      </thead>
      <tbody id="enquiryData">
        @if(count($enquiries) > 0)
          @foreach($enquiries as $index => $enquiry)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$enquiry->name}}</td>
            <td>{{$enquiry->student_no}}</td>
            <td>
              @if(!empty($enquiry->course))
                @php
                  $course = '';
                  foreach(explode(',', $enquiry->course) as $index => $courseName){
                    if(0 == $index){
                      $course =$courseName;
                    }else{
                      $course .=','.$courseName;
                    }
                  }
                @endphp
                {{$course}}
              @endif
            </td>
            <td>
              <a href="{{url('admin/enquiry')}}/{{$enquiry->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30'/>
                </a>
            </td>
            @if(2 == $adminUser->type)
              <td>
              <a id="{{$enquiry->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30'/>
                  </a>
                  <form id="deleteEnquiry_{{$enquiry->id}}" action="{{url('admin/delete-enquiry')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="enquiry_id" value="{{$enquiry->id}}">
                  </form>
              </td>
            @endif
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No enquiry.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">
  $(function () {
    $("#from").datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
          todayHighlight: true
    });
    $("#to").datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
          todayHighlight: true
    });
  });

  function showEnquiry(ele){
    var fromDate = document.getElementById('from').value;
    var toDate = document.getElementById('to').value;
    var course = $(ele).val();
    if(new Date(fromDate) > new Date(toDate)){
      $.alert({
        title: 'Alert',
        content: 'from date should be less than or equal to to date.',
      });
    } else if(course){
      $.ajax({
        method: "POST",
        url: "{{url('admin/get-enquiry-by-course')}}",
        data: {from_date:fromDate,to_date:toDate,course:course}
      })
      .done(function( result ) {
        var enquiryData = document.getElementById('enquiryData');
        enquiryData.innerHTML = '';
        if( 0 < result.length){
          $.each(result, function(idx, obj) {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleName = document.createElement('td');
            eleName.innerHTML = obj.name;
            eleTr.appendChild(eleName);

            var eleStudentNo = document.createElement('td');
            eleStudentNo.innerHTML = obj.student_no;
            eleTr.appendChild(eleStudentNo);

            var eleCourse = document.createElement('td');
            eleCourse.innerHTML = obj.course;
            eleTr.appendChild(eleCourse);

            var eleEdit = document.createElement('td');
            var editUrl = "{{url('admin/enquiry')}}/"+obj.id+'/edit';
            var editImage = '<img src="{{asset('images/edit1.png')}}" width="30" height="30"/>';
            eleEdit.innerHTML = '<a href="'+editUrl+'">'+editImage+'</a>' ;
            eleTr.appendChild(eleEdit);

            var eleDelete = document.createElement('td');
            var deleteUrl = "{{url('admin/delete-enquiry')}}";
            var imageUrl  = "{{asset('images/delete2.png')}}";
            var csrfField = '{{ csrf_field() }}';
            var methodField = '{{ method_field('DELETE') }}';
            eleDelete.innerHTML = '<a id="'+obj.id+'" onclick="confirmDelete(this);"><img src="'+imageUrl+'" width="30" height="30" title="Delete" /></a><form id="deleteEnquiry_'+obj.id+'" action="'+deleteUrl+'" method="POST" style="display: none;">'+csrfField+''+methodField+'<input type="hidden" name="enquiry_id" value="'+obj.id+'"></form>';
            eleTr.appendChild(eleDelete);
            enquiryData.appendChild(eleTr);
          });
        } else {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No Result';
          eleIndex.setAttribute('colspan', '6');
          eleTr.appendChild(eleIndex);
          enquiryData.appendChild(eleTr);
        }
      });
    }
  }

  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'Are You sure, you want to delete this enquiry',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteEnquiry_'+id;
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