<html>
<head>
<title>Enquiry</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style type="text/css">
	body{
		background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
						  url('../images/ca_front.jpg');
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center;
		background-attachment: fixed;
		width: 100%;
		height: auto;
	}
	.container{

		display: flex;
		justify-content: center;
		margin-top:5%;
		font-size: 17px;
		font-family: georgia;
		color: #fff;
	}
	.login-div{
		background: rgba(86, 101, 115 ,0.5);
		position: absolute;
		padding: 30px 40px;
		width: 80%;

	}
	.head{
		letter-spacing: 3px;
		font-size: 30px;
		text-align: center;
	}
	.btn-login{
		width: 100%;
		font-size: 20px;
		letter-spacing: 1px;
		font-weight: bold;
		border-radius: 15px;
		background-color: #4caf50;
		color: #fff;
		border-color: #4caf50;
	}
	@media only screen and (max-width: 560px){
		.login-div{
		background: rgba(86, 101, 115 ,0.5);
		position: absolute;
		padding: 30px 40px;
		width: 90%;
		}
	}
	@media only screen and (max-width: 400px){
		.login-div{
		width: 100%;
		}
	}
	@media only screen and (max-width: 767px){
		select,input[type=text]{
			margin-bottom: 5px;
		}
	}
	sup{
		color: red;
	}
</style>
<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
</script>
</head>
<body>
	<div class="container">
		<div class="login-div">
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
	    <span style="float: right;"> <sup>*</sup>- fields are mandatory</span>
		<h1 class="head">Enquiry Form</h1>
		<form method="POST" action="{{url('enquiry')}}">
          {{csrf_field()}}
	       	<div class="form-group row">
	       		<label class="col-sm-3 col-form-label">Name:<sup>*</sup></label>
      			<div class="col-sm-6">
	            	<input type="text" class="form-control" name="name" required="true" placeholder="user name">
	            </div>
	        </div>
	        <div class="form-group row">
	       		<label class="col-sm-3 col-form-label">Enquiry For Course:</label>
      			<div class="col-sm-9">
	            	<input type="checkbox" name="course_name[]" value="12th"> 12th
	            	<input type="checkbox" name="course_name[]" value="CPT"> CPT
	            	<input type="checkbox" name="course_name[]" value="PICC"> PICC
	            	<input type="checkbox" name="course_name[]" value="Other" id="other"> Other
	            </div>
	        </div>
	        <div class="form-group row hide" id="other_course">
	       		<label class="col-sm-3 col-form-label">Other Course:</label>
      			<div class="col-sm-6">
	            	<input type="text" id="other_str" name="other" class="form-control" placeholder="Other">
	            </div>
	        </div>
	        <div class="form-group row">
	        	<label class="col-sm-3 col-form-label">10th:</label>
      			<div class="col-sm-2">
	            	<select name="ssc_medium" class="form-control">
		            	<option value=""> Medium</option>
		            	<option value="English">English</option>
		            	<option value="Hindi">Hindi</option>
		            	<option value="Marathi">Marathi</option>
		            </select>
	            </div>
	            <div class="col-sm-2">
	            	<select name="ssc_stream" class="form-control">
		            	<option value=""> Stream</option>
		            	<option value="Science">Science</option>
		            	<option value="Commerce">Commerce</option>
		            	<option value="Arts">Arts</option>
		            </select>
	            </div>
	            <div class="col-sm-5">
	            	<input type="text" name="ssc_school" class="form-control" placeholder="School">
	            </div>
	        </div>
	        <div class="form-group row">
	        	<label class="col-sm-3 col-form-label">12th:</label>
      			<div class="col-sm-2">
	            	<select name="hsc_medium" class="form-control">
		            	<option value=""> Medium</option>
		            	<option value="English">English</option>
		            	<option value="Hindi">Hindi</option>
		            	<option value="Marathi">Marathi</option>
		            </select>
	            </div>
	            <div class="col-sm-2">
	            	<select name="hsc_stream" class="form-control">
		            	<option value=""> Stream</option>
		            	<option value="Science">Science</option>
		            	<option value="Commerce">Commerce</option>
		            	<option value="Arts">Arts</option>
		            </select>
	            </div>
	            <div class="col-sm-5">
	            	<input type="text" name="hsc_school" class="form-control" placeholder="School">
	            </div>
	        </div>
	        <div class="form-group row">
	        	<label class="col-sm-3 col-form-label">Graduation:</label>
      			<div class="col-sm-2">
	            	<select name="graduation_medium" class="form-control">
		            	<option value=""> Medium</option>
		            	<option value="English">English</option>
		            	<option value="Hindi">Hindi</option>
		            	<option value="Marathi">Marathi</option>
		            </select>
	            </div>
	            <div class="col-sm-2">
	            	<select name="graduation_stream" class="form-control">
		            	<option value=""> Stream</option>
		            	<option value="Science">Science</option>
		            	<option value="Commerce">Commerce</option>
		            	<option value="Arts">Arts</option>
		            </select>
	            </div>
	            <div class="col-sm-5">
	            	<input type="text" name="graduation_school" class="form-control" placeholder="School">
	            </div>
	        </div>
	        <div class="form-group row">
	        	<label class="col-sm-3 col-form-label">Address:</label>
      			<div class="col-sm-6">
	            	<input type="text" class="form-control" name="address" placeholder="address">
	            </div>
	        </div>
	        <div class="form-group row">
	        	<label class="col-sm-3 col-form-label">City:</label>
      			<div class="col-sm-3">
	            	<select name="city" class="form-control">
		            	<option value=""> City</option>
		            	<option value="Amravati">Amravati</option>
		            </select>
	            </div>
	        </div>
	        <div class="form-group row">
	        	<label class="col-sm-3 col-form-label">Contact No:<sup>*</sup></label>
      			<div class="col-sm-3">
	            	<input type="text" class="form-control" name="student_no" placeholder="Student No - Required" required="true" pattern="[0-9]{10}">
	            </div>
	            <div class="col-sm-3">
	            	<input type="text" class="form-control" name="parent_no" placeholder="Parent No - Optional">
	            </div>
	            <div class="col-sm-3">
	            	<input type="text" class="form-control" name="land_line_no" placeholder="Land Line No - Optional">
	            </div>
	        </div>
	        <div class="form-group row">
	       		<label class="col-sm-3 col-form-label">Reference By:</label>
      			<div class="col-sm-6">
	            	<input type="text" class="form-control" name="reference_by" placeholder="Reference By">
	            </div>
	        </div>
	        <input type="hidden" name="enquiry_by" value="student">
	        <div class="form-group row">
	        	<div class="col-sm-2">
	            	<button class="btn btn-default btn-login" type="submit" >Submit</button>
	            </div>
	        </div>
	    </form>
	    </div>
    </div>
</body>
<script type="text/javascript">
  	$(document).ready(function(){
        setTimeout(function() {
          $('.alert-success').fadeOut('fast');
        }, 10000); // <-- time in milliseconds
    });
  	$('#other').click(function(){
	  	if($('#other_course').hasClass('hide')){
	  		$('#other_course').removeClass('hide');
	  	} else {
	  		$('#other_course').addClass('hide');
	  	}
	  	$('#other_str').val(' ');
  	});
</script>
</html>