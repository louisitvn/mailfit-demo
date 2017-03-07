<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title')</title>
	
	@include('layouts._favicon')
	
	@include('layouts._head')
	
	@include('layouts._css')
	
	@include('layouts._js')
	
	@include('layouts._user_script')
	
</head>

<body class="navbar-top color-scheme-{{ Auth::user()->getFrontendScheme() }}">
	
	<!-- Page header -->
	<div class="page-header">
		<div class="page-header-content">
			
			@yield('page_header')
			
		</div>
	</div>
	<!-- /page header -->

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">
			
				<!-- display flash message -->
				@include('common.errors')
				
				<!-- main inner content -->
				@yield('content')
                
				<div class="bottom_hook"></div>
			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

		<!-- Footer -->
		<div class="footer text-muted">
			{!! trans('messages.copy_right') !!}
		</div>
		<!-- /footer -->

	</div>
	<!-- /page container -->
	
	@include("layouts._modals")
	
	<br style="clear:both" />	
</body>
</html>
