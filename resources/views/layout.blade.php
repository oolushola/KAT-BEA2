
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title')</title>
	<noscript>Hey, you need to enable javascript before using this application</noscript>
    
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Kaya - Logistics Redefined </title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" media="all">
	<link href="{{URL::asset('global_assets/css/icons/icomoon/styles.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/bootstrap_limitless.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/layout.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/components.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{URL::asset('assets/css/colors.min.css')}}" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="{{URL::asset('css/custom.css')}}" type="text/css" />
	<link rel="stylesheet" href="{{URL::asset('css/print-master.css')}}" type="text/css" media="all" />
	@yield('css')


</head>

<body>
@if(Auth::user())
<?php $auth = Auth::user()->role_id; ?>
	<!-- Main navbar -->
	<div class="navbar navbar-expand-md navbar-dark">
		<p style="font-size:20px; margin:0; padding:0; margin-top:8px;">Káyá Africa Technology</p>
		

		<div class="d-md-none">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
				<i class="icon-tree5"></i>
			</button>
			<button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
				<i class="icon-paragraph-justify3"></i>
			</button>
		</div>

		<div class="collapse navbar-collapse" id="navbar-mobile">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
						<i class="icon-paragraph-justify3"></i>
					</a>
				</li>
			</ul>

			<span class="badge bg-success ml-md-3 mr-md-auto">beta version </span>

			<ul class="navbar-nav">
				

				<li class="nav-item dropdown">
					<a href="#" class="navbar-nav-link dropdown-toggle caret-0" data-toggle="dropdown">
						<i class="icon-bubbles4"></i>
						<span class="d-md-none ml-2">Messages</span>
						<span class="badge badge-pill bg-warning-400 ml-auto ml-md-0"></span>
					</a>
					
					<div class="dropdown-menu dropdown-menu-right dropdown-content wmin-md-350">
						<div class="dropdown-content-header">
							<span class="font-weight-semibold">Messages</span>
							<a href="#" class="text-default"><i class="icon-compose"></i></a>
						</div>

						<div class="dropdown-content-body dropdown-scrollable">
							<ul class="media-list">
								<li class="media">
									<div class="mr-3 position-relative">
										<img src="../../../../global_assets/images/demo/users/face10.jpg" width="36" height="36" class="rounded-circle" alt="">
									</div>

									<div class="media-body">
										<div class="media-title">
											<a href="#">
												<span class="font-weight-semibold">James Alexander</span>
												<span class="text-muted float-right font-size-sm">04:58</span>
											</a>
										</div>

										<span class="text-muted">who knows, maybe that would be the best thing for me...</span>
									</div>
								</li>
							</ul>
						</div>

						<div class="dropdown-content-footer justify-content-center p-0">
							<a href="#" class="bg-light text-grey w-100 py-2" data-popup="tooltip" title="Load more"><i class="icon-menu7 d-block top-0"></i></a>
						</div>
					</div>
				</li>

				<li class="nav-item dropdown dropdown-user">
					<a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
						@if(Auth::user()->photo != '')
							<img src="{{URL::asset('/assets/img/users')}}/{{Auth::user()->photo}}" class="rounded-circle mr-2" height="34" alt="profile-photo">
						@else
							<span class="icon-user" style="font-size:20px;"></span>
						@endif
						<span>{{ucwords(Auth::user()->first_name)}}</span>
					</a>

					<div class="dropdown-menu dropdown-menu-right">
						<a href="#uploadPhotoBox" data-toggle="modal" class="dropdown-item">
							<i class="icon-file-picture"></i> Upload Photo
						</a>
						<a href="#changePassword" data-toggle="modal" class="dropdown-item">
							<i class="icon-lock"></i>Change Password
						</a>
						<!-- <a href="#" class="dropdown-item"><i class="icon-comment-discussion"></i> Messages <span class="badge badge-pill bg-blue ml-auto">58</span></a> -->
						<div class="dropdown-divider"></div>
						<a href="{{URL('logout')}}" class="dropdown-item"><i class="icon-switch2"></i> Logout</a>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page content -->
	<div class="page-content">

		<!-- Main sidebar -->
		<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

			<!-- Sidebar mobile toggler -->
			<div class="sidebar-mobile-toggler text-center">
				<a href="#" class="sidebar-mobile-main-toggle">
					<i class="icon-arrow-left8"></i>
				</a>
				Navigation
				<a href="#" class="sidebar-mobile-expand">
					<i class="icon-screen-full"></i>
					<i class="icon-screen-normal"></i>
				</a>
			</div>


			<div class="sidebar-content">
				<div class="sidebar-user">
					<div class="card-body">
						<div class="media">
							<div class="mr-3">
								@if(Auth::user()->photo != '')
								<a href="#"><img src="{{URL::asset('/assets/img/users')}}/{{Auth::user()->photo}}" width="38" height="38" class="rounded-circle" alt="Profile Photo"></a>
								@else
									<span class="icon-user" style="font-size:40px;"></span>
								@endif

							</div>

							<div class="media-body">
								<div class="media-title font-weight-semibold">{{ucwords(Auth::user()->first_name)}} {{ucwords(Auth::user()->last_name)}}</div>
								<div class="font-size-xs opacity-50">
									<i class="icon-pin font-size-sm"></i> &nbsp;
									
									@if(Auth::user()->role_id ==1) Super Admin @endif
									@if(Auth::user()->role_id == 2) Admin Officer @endif
									@if(Auth::user()->role_id == 3) Finance Officer @endif
									@if(Auth::user()->role_id == 4) Visibility Officer @endif
									@if(Auth::user()->role_id == 5) Field Ops Officer @endif
									@if(Auth::user()->role_id == 6) Transport Supervisor @endif
								</div>
							</div>

							<div class="ml-3 align-self-center">
								<a href="#" class="text-white"><i class="icon-cog3"></i></a>
							</div>
						</div>
					</div>
				</div>

				<div class="card card-sidebar-mobile">
					<ul class="nav nav-sidebar" data-nav-type="accordion">

						@if($auth >= 1 && $auth <=4)
						<li class="nav-item">
							<a href="{{URL('dashboard')}}" class="nav-link">
								<i class="icon-home4"></i>
								<span>Dashboard</span>
							</a>
						</li>
						@endif

						@if($auth == 1 || $auth == 2)
						<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Administrative Purpose</div> <i class="icon-menu" title="Main"></i></li>

						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-cog"></i> <span>Preference Settings</span></a>

							<ul class="nav nav-group-sub" data-submenu-title="Starter kit">
								<li class="nav-item"><a href="{{URL('companies-profile')}}" class="nav-link active">Kaya Profile</a></li>
								<li class="nav-item"><a href="{{URL('product-category')}}" class="nav-link">Product Category</a></li>
								<li class="nav-item">
                                    <a href="{{URL('products')}}" class="nav-link">Products</a>
                                </li>
								<li class="nav-item">
									<a href="{{URL('truck-types')}}" class="nav-link">Truck Types</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('loading-sites')}}" class="nav-link">Loading Sites</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('cargo-availability')}}" class="nav-link">Available Cargo</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('kaya-target')}}" class="nav-link">Targets</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('invoice-subheading')}}" class="nav-link">Invoice Subheading</a>
								</li>
							</ul>
						</li>
						

						<li class="nav-item-header">
                            <div class="text-uppercase font-size-xs line-height-xs">Users</div> 
                            <i class="icon-menu" title="Forms"></i>
                        </li>
                        
						<li class="nav-item">
							<a href="{{URL('user-registration')}}" class="nav-link">
								<i class="icon-user-plus"></i>
								<span>Register User</span>
								<span class="badge bg-success align-self-center ml-auto"></span>
							</a>
						</li>
						
						<li class="nav-item-header">
                            <div class="text-uppercase font-size-xs line-height-xs">Our Clients</div> 
                            <i class="icon-menu" title="Forms"></i>
                        </li>

						<li class="nav-item">
							<a href="{{URL('clients')}}" class="nav-link">
								<i class="icon-list-ordered"></i>
								<span>Clients</span>
								<span class="badge bg-success align-self-center ml-auto">{!! Session::get('client') !!} Active Clients</span>
							</a>
						</li>

						@endif

						<li class="nav-item-header">
                            <div class="text-uppercase font-size-xs line-height-xs">TRIPS
							@if(Auth::user()->role_id <= 4)
							<span class="badge bg-blue-400 align-right ml-auto">{!! Session::get('on_journey') !!} Unresolved</span>
							@endif
							</div> 
                            <i class="icon-menu" title="Forms"></i>
                        </li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link {{request()->is('view-orders') ? 'active' : ''}}"><i class="icon-cube3"></i> <span>Orders</span></a>

							<ul class="nav nav-group-sub" data-submenu-title="Starter kit">
								@if($auth == 1 || $auth == 2 || $auth == 4 || $auth == 5)
								<li class="nav-item">
									<a href="{{URL('truck-availability')}}" class="nav-link">
										Truck Availability</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('trips')}}" class="nav-link {{request()->is('trips') ? 'active' : ''}}">
										Create New Trip</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('truck-availability-list')}}" class="nav-link {{request()->is('trips') ? 'active' : ''}}">
										Create Trip From Availability</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('update-trip')}}" class="nav-link">Update Existing Trip</a>
								</li>
								@endif

								@if($auth !== 5)
								<li class="nav-item">
									<a href="{{URL('view-orders')}}" class="nav-link">
									Trip Database</a>
								</li>
								<li class="nav-item">
									<a href="{{URL('voided-trips')}}" class="nav-link">
									Voided Trips</a>
								</li>
								<li class="nav-item"><a href="{{URL('generate-report')}}" class="nav-link">Report</a></li>
								@endif
							</ul>
						</li>


						@if($auth == 1 || $auth == 3)
						<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">FINANCE</div> <i class="icon-menu" title="Forms"></i></li>
                        
						<li class="nav-item">
							<a href="{{URL('financials/overview')}}" class="nav-link">
								<i class="icon-eye"></i>
								<span>Overview</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{URL('financials/dashboard')}}" class="nav-link">
								<i class="icon-coins"></i>
								<span>Financials</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{URL('transporter-rate')}}" class="nav-link">
								<i class="icon-calculator3"></i>
								<span>Transporter Rate</span>
							</a>
						</li>

                        <li class="nav-item">
							<a href="{{URL('payment-request')}}" class="nav-link">
								<i class="icon-git-pull-request"></i>
								<span>Payment Request</span>
								<span class="badge bg-danger align-self-center ml-auto">
									{!! Session::get('payment_request') !!} Pending
								</span>
							</a>
						</li>

						<li class="nav-item">
							<a href="{{URL('invoices')}}" class="nav-link">
								<i class="icon-pencil3"></i>
								<span>Invoices</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{URL('all-invoiced-trips')}}" class="nav-link">
								<i class="icon-coins"></i>
								<span>Invoiced</span>
							</a>
						</li>

						<li class="nav-item">
							<a href="{{URL('bulk-payment')}}" class="nav-link">
								<i class="icon-spray"></i>
								<span>Bulk Payment</span>
							</a>
						</li>

						<li class="nav-item">
							<a href="{{URL('incentives')}}" class="nav-link">
								<i class="icon-spray"></i>
								<span>Incentives</span>
							</a>
						</li>

						<li class="nav-item">
							<a href="{{URL('local-purchase-order')}}" class="nav-link">
								<i class="icon-feed"></i>
								<span>L.P.O.</span>
							</a>
						</li>

						<li class="nav-item">
							<a href="{{URL('vat-rate')}}" class="nav-link">
								<i class="icon-coins"></i>
								<span>Vat Rate</span>
							</a>
						</li>
						@endif

						
						@if($auth == 1 || $auth == 2)
						<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">TRANSPORTATION</div> <i class="icon-menu" title="Components"></i></li>

						<li class="nav-item">
							<a href="{{URL('transporters')}}" class="nav-link">
								<i class="icon-train2"></i>
								<span>Transporter</span>
							</a>
						</li>
                        <li class="nav-item">
							<a href="{{URL('trucks')}}" class="nav-link">
								<i class="icon-truck"></i>
								<span>Trucks</span>
							</a>
						</li>
                        <li class="nav-item">
							<a href="{{URL('drivers')}}" class="nav-link">
								<i class="icon-steering-wheel"></i>
								<span>Drivers</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{URL('assign-driver-truck')}}" class="nav-link">
								<i class="icon-shutter"></i>
								<span>Pair Truck With Driver</span>
							</a>
						</li>
						@endif
					</ul>
				</div>
			</div>			
		</div>


		<!-- Main content -->
		<div class="content-wrapper">
            <div class="content">
				@yield('main')
            </div>

			

			<!-- Footer -->
			<div class="navbar navbar-expand-lg navbar-light">
				<div class="text-center d-lg-none w-100">
					<button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
						<i class="icon-unfold mr-2"></i>
						Footer
					</button>
				</div>

				<div class="navbar-collapse collapse" id="navbar-footer">
					<span class="navbar-text">
						&copy; {!! date('Y') !!} <a href="http://kayaafrica.co" target="_blank">Powered by Kayaafrica</a>
					</span>

					<ul class="navbar-nav ml-lg-auto">
						<li class="nav-item"><a href="#" class="navbar-nav-link" target="_blank"><i class="icon-lifebuoy mr-2"></i> Developed with <i class="icon-heart5"></i> by Kayaafrica Tech Team</a></li>
					</ul>
				</div>
			</div>
			<!-- /footer -->

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	@include('general-partials._upload-profile-photo')

	@include('general-partials._update-password')


<script src="{{URL::asset('global_assets/js/main/jquery.min.js')}}"></script>
<script src="{{URL::asset('global_assets/js/main/bootstrap.bundle.min.js')}}"></script>
<script src="{{URL::asset('global_assets/js/plugins/loaders/blockui.min.js')}}"></script>
<script src="{{URL::asset('global_assets/js/plugins/forms/styling/switchery.min.js')}}"></script>
<script src="{{URL::asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
<script src="{{URL::asset('global_assets/js/plugins/forms/styling/uniform.min.js')}}"></script>
<script src="{{URL::asset('assets/js/app.js')}}"></script>
<script src="{{URL::asset('global_assets/js/demo_pages/form_layouts.js')}}"></script>

<script type="text/javascript" src="{{URL::asset('/js/validator/jquery.form.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('/js/validator/validatefile.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('/js/validator/dashboard.js')}}"></script>



@yield('script')
@endif


</body>
</html>
