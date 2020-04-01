<html>
<head>
<title>Login</title>
<link href="{{ asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
<link href="{{ asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet"/>
<script src="{{ asset('js/jquery.min.js?ver=1.0')}}"></script>
<script src="{{ asset('js/bootstrap.min.js?ver=1.0')}}"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style type="text/css">
	body{
		background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
						  url('../images/Loginbg.jpeg');
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
		margin-top:15%;
		font-size: 17px;
		font-family: georgia;
		color: #fff;
	}
	.login-div{
		background: rgba(86, 101, 115 ,0.5);
		position: absolute;
		padding: 30px 40px;
		width: 500px;

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
		width: 300px;

	}
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
		<h1 class="head">LOGIN</h1>
		<form method="POST" action="{{url('admin/login')}}">
          {{csrf_field()}}
	       	<div class="form-group">
	            <label class="control-label">Username</label>
	            <div class="input-group">
	              <span class="input-group-addon"><i class="fa fa-user"></i></span>
	              <input type="email" class="form-control" name="email" required="true">
	            </div>
	        </div>
	        <div class="form-group">
	            <label class="control-label">Password</label>
	            <div class="input-group">
	              <span class="input-group-addon"><i class="fa fa-key"></i></span>
	              <input type="password" class="form-control" name="password" required="true">
	            </div>
	        </div>
	        <button class="btn btn-default btn-login" type="submit" >Login </button>
	    </form>
	    </div>
    </div>
</body>
</html>