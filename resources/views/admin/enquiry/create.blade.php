@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Enquiry </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Studet Admission </li>
      <li class="active"> Enquiry </li>
    </ol>
  </section>
  <style type="text/css">
    sup{
      color: red;
    }
    @media only screen and (max-width: 767px){
      select,input[type=text]{
        margin-bottom: 5px;
      }
    }
  </style>
@stop
@section('admin_content')
  <div class="container admin_div">
    <span style="float: right;"> <sup>*</sup>- fields are mandatory</span>
    <h3 class="head" align="center">Enquiry Form</h3>
    @if(count($errors) > 0)
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif
    @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <form method="POST" action="{{url('admin/create-enquiry')}}">
        {{csrf_field()}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Name:<sup>*</sup></label>
          <div class="col-sm-6">
              <input type="text" class="form-control" name="name" required="true" placeholder="user name" value="{{$enquiry->name}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Enquiry For Course: </label>
          <div class="col-sm-9">
            @php
              $courseArr = [];
              if(!empty($enquiry->course)){
                $courseArr = explode(',', $enquiry->course);
              }
            @endphp
            @if(isset($enquiry->id))
              <input type="checkbox" name="course_name[]" value="12th" @if(in_array('12th', $courseArr)) checked @endif disabled > 12th
              <input type="checkbox" name="course_name[]" value="CPT" @if(in_array('CPT', $courseArr)) checked @endif disabled > CPT
              <input type="checkbox" name="course_name[]" value="PICC" @if(in_array('PICC', $courseArr)) checked @endif disabled > PICC
              <input type="checkbox" name="course_name[]" value="Other" id="other" @if(in_array('Other', $courseArr)) checked @endif disabled > Other
            @else
              <input type="checkbox" name="course_name[]" value="12th"> 12th
              <input type="checkbox" name="course_name[]" value="CPT"> CPT
              <input type="checkbox" name="course_name[]" value="PICC"> PICC
              <input type="checkbox" name="course_name[]" value="Other" id="other"> Other
            @endif
          </div>
        </div>
        @if(empty($enquiry->other))
          <div class="form-group row hide" id="other_course">
        @else
          <div class="form-group row" id="other_course">
        @endif
          <label class="col-sm-3 col-form-label">Other Course:</label>
          <div class="col-sm-6">
              <input type="text" id="other_str" name="other" class="form-control" placeholder="Other" value="{{$enquiry->other}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">10th:</label>
          @php
            $sscArr = [];
            if(!empty($enquiry->ssc)){
              $sscArr = explode('|', $enquiry->ssc);
            }
          @endphp
          <div class="col-sm-2">
            <select name="ssc_medium" class="form-control" @if(isset($enquiry->id)) disabled @endif>
              <option value=""> Medium</option>
              <option value="English" @if(in_array('English', $sscArr)) selected @endif>English</option>
              <option value="Hindi" @if(in_array('Hindi', $sscArr)) selected @endif>Hindi</option>
              <option value="Marathi" @if(in_array('Marathi', $sscArr)) selected @endif>Marathi</option>
            </select>
          </div>
          <div class="col-sm-2">
            <select name="ssc_stream" class="form-control" @if(isset($enquiry->id)) disabled @endif>
              <option value=""> Stream</option>
              <option value="Science" @if(in_array('Science', $sscArr)) selected @endif>Science</option>
              <option value="Commerce" @if(in_array('Commerce', $sscArr)) selected @endif>Commerce</option>
              <option value="Arts" @if(in_array('Arts', $sscArr)) selected @endif>Arts</option>
            </select>
          </div>
          <div class="col-sm-5">
            <input type="text" name="ssc_school" class="form-control" placeholder="School" value="{{(isset($sscArr[2]))?$sscArr[2]:''}}" @if(isset($enquiry->id)) readonly @endif>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">12th:</label>
          @php
            $hscArr = [];
            if(!empty($enquiry->hsc)){
              $hscArr = explode('|', $enquiry->hsc);
            }
          @endphp
          <div class="col-sm-2">
              <select name="hsc_medium" class="form-control" @if(isset($enquiry->id)) disabled @endif>
                <option value=""> Medium</option>
                <option value="English"@if(in_array('English', $hscArr)) selected @endif>English</option>
                <option value="Hindi"@if(in_array('Hindi', $hscArr)) selected @endif>Hindi</option>
                <option value="Marathi"@if(in_array('Marathi', $hscArr)) selected @endif>Marathi</option>
              </select>
            </div>
            <div class="col-sm-2">
              <select name="hsc_stream" class="form-control" @if(isset($enquiry->id)) disabled @endif>
                <option value=""> Stream</option>
                <option value="Science"@if(in_array('Science', $hscArr)) selected @endif>Science</option>
                <option value="Commerce"@if(in_array('Commerce', $hscArr)) selected @endif>Commerce</option>
                <option value="Arts"@if(in_array('Arts', $hscArr)) selected @endif>Arts</option>
              </select>
            </div>
            <div class="col-sm-5">
              <input type="text" name="hsc_school" class="form-control" placeholder="School" value="{{(isset($hscArr[2]))?$hscArr[2]:''}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Graduation:</label>
          @php
            $graduationArr = [];
            if(!empty($enquiry->graduation)){
              $graduationArr = explode('|', $enquiry->graduation);
            }
          @endphp
          <div class="col-sm-2">
              <select name="graduation_medium" class="form-control" @if(isset($enquiry->id)) disabled @endif>
                <option value=""> Medium</option>
                <option value="English"@if(in_array('English', $graduationArr)) selected @endif>English</option>
                <option value="Hindi"@if(in_array('Hindi', $graduationArr)) selected @endif>Hindi</option>
                <option value="Marathi"@if(in_array('Marathi', $graduationArr)) selected @endif>Marathi</option>
              </select>
            </div>
            <div class="col-sm-2">
              <select name="graduation_stream" class="form-control" @if(isset($enquiry->id)) disabled @endif>
                <option value=""> Stream</option>
                <option value="Science"@if(in_array('Science', $graduationArr)) selected @endif>Science</option>
                <option value="Commerce"@if(in_array('Commerce', $graduationArr)) selected @endif>Commerce</option>
                <option value="Arts"@if(in_array('Arts', $graduationArr)) selected @endif>Arts</option>
              </select>
            </div>
            <div class="col-sm-5">
              <input type="text" name="graduation_school" class="form-control" placeholder="School" value="{{(isset($graduationArr[2]))?$graduationArr[2]:''}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Address:</label>
          <div class="col-sm-6">
              <input type="text" class="form-control" name="address" placeholder="address" value="{{$enquiry->address}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">City:</label>
          <div class="col-sm-3">
              <select name="city" class="form-control" @if(isset($enquiry->id)) disabled @endif>
                <option value=""> City</option>
                <option value="Amravati" @if('Amravati' == $enquiry->city) selected @endif>Amravati</option>
              </select>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Contact No:<sup>*</sup></label>
          <div class="col-sm-3">
              <input type="text" class="form-control" name="student_no" placeholder="Student No - Required" required="true" pattern="[0-9]{10}" value="{{$enquiry->student_no}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
            <div class="col-sm-3">
              <input type="text" class="form-control" name="parent_no" placeholder="Parent No - Optional" value="{{$enquiry->parent_no}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
            <div class="col-sm-3">
              <input type="text" class="form-control" name="land_line_no" placeholder="Land Line No - Optional" value="{{$enquiry->land_line_no}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Student Interest:</label>
          <div class="col-sm-3">
              <select name="student_interest" class="form-control" @if(isset($enquiry->id)) disabled @endif>
                <option value="0"> Student Interest</option>
                <option value="1"@if(1 == $enquiry->student_interest) selected @endif>1</option>
                <option value="2"@if(2 == $enquiry->student_interest) selected @endif>2</option>
                <option value="3"@if(3 == $enquiry->student_interest) selected @endif>3</option>
                <option value="4"@if(4 == $enquiry->student_interest) selected @endif>4</option>
                <option value="5"@if(5 == $enquiry->student_interest) selected @endif>5</option>
              </select>
            </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Reference By:</label>
          <div class="col-sm-6">
              <input type="text" class="form-control" name="reference_by" placeholder="Reference By" value="{{$enquiry->reference_by}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        @if(isset($enquiry->id))
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Enquiry By:</label>
          <div class="col-sm-6">
              <input type="text" class="form-control" name="enquiry_by" placeholder="Enquiry By" value="{{$enquiry->enquiry_by}}" @if(isset($enquiry->id)) readonly @endif>
            </div>
        </div>
        @else
          <input type="hidden" name="enquiry_by" value="{{Auth::guard('admin')->user()->name}}">
        @endif
        <div class="form-group row">
          <div class="col-sm-2">
            @if(!isset($enquiry->id))
              <button class="btn btn-default btn-login" type="submit" >Submit</button>
            @else
              <a href="{{ url('admin/enquiries') }}" class="btn btn-default">Back</a>
            @endif
            </div>
        </div>
    </form>
    <input type="hidden" id="enquiry_id" value="{{$enquiry->id}}">
  </div>
<script type="text/javascript">
  $(document).ready(function(){
    $('#other').click(function(){
      if($('#other_course').hasClass('hide')){
        $('#other_course').removeClass('hide');
      } else {
        $('#other_course').addClass('hide');
      }
      if(!$('#enquiry_id').val()){
        $('#other_str').val(' ');
      }
    });
  });
</script>
@stop
