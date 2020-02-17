@if(isset(Auth::user()->email))
	<script>window.location='/dashboard';</script>							
@endif

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Kaya - Logistics Redefined </title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('global_assets/css/icons/icomoon/styles.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/bootstrap_limitless.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/layout.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/components.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/colors.min.css')}}" rel="stylesheet" type="text/css">
</head>
<body style="background:url('{{url('/assets/img/login-background.jpg')}}'); background-size:cover;">

	

	<!-- Page content -->
	<div class="page-content">
		<div class="content-wrapper">

			<div class="content d-flex justify-content-center align-items-center">
				
				<form class="login-form" method="POST" action="{{URL('check-login')}}" id="frmLogin">
					@csrf
					<div class="card mb-0"  style="opacity:0.8; filter: alpha(opacity=50);">
						<div class="card-body">
							<div class="text-center mb-3">
								<img src="{{URL::asset('/assets/img/kaya/kaya-africa-techonology-nig-ltd.PNG')}}" class="text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1" width="150" height="150"></i>
								<h5 class="mb-0 font-weight-semibold" >Login</h5>
								<span class="d-block text-muted">Enter your credentials below</span>
							</div>

							

							@if ($message = Session::get('error'))
								<div class="alert alert-danger alert-block">
									<button type="button" class="close" data-dismiss="alert">x</button>
									{{$message}}
								</div>
							@endif

							@if(count($errors) > 0)
								<div class="alert alert-danger">
									<ul>
										@foreach($errors->all() as $error)
											<li>{{$error}}</li>
										@endforeach
									</ul>
								</div>
							@endif
				

							<div class="form-group form-group-feedback form-group-feedback-left">
								<input type="text" class="form-control" placeholder="Email" name="email" value="{{old('email')}}">
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
								<input type="password" class="form-control" placeholder="Password" name="password">
								<div class="form-control-feedback">
									<i class="icon-lock2 text-muted"></i>
								</div>
							</div>

							<div class="form-group">
								<button type="submit" id="login" class="btn btn-primary btn-block">Sign in <i class="icon-circle-right2 ml-2"></i></button>
							</div>

							<div class="text-center">
								<a href="login_password_recover.html">Forgot password?</a>
							</div>
						</div>
					</div>
				</form>
			</div>

			
		</div>
	</div>


<script src="{{URL::asset('global_assets/js/main/jquery.min.js')}}"></script>
<script src="{{URL::asset('global_assets/js/main/bootstrap.bundle.min.js')}}"></script>
<script src="{{URL::asset('global_assets/js/plugins/loaders/blockui.min.js')}}"></script>
<script src="{{URL::asset('/js/validator/jquery.form.js')}}" type="text/javascript"></script>
<script src="assets/js/app.js"></script>
<script src="{{URL::asset('/js/validator/auth.js')}}" type="text/javascript"></script>

</body>
</html>
