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

<body class="navbar-top  color-scheme-{{ Auth::user()->getBackendScheme() }}">

	
	
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
	
	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#delete_confirm_model">aa</a>
	<!-- Basic modal -->
	<div id="delete_confirm_model" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="confirm-delete-form" onkeypress="return event.keyCode != 13;">
					<div class="modal-header bg-danger">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2 class="modal-title">{{ trans('messages.are_you_sure') }}</h2>
					</div>
	
					<div class="modal-body">
						
							<h6></h6>
							
							<div class="form-group">
								<label>{!! trans('messages.type_delete_to_confirm') !!}</label>
								<input class="form-control" name="delete" />
							</div>
						
					</div>
	
					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn btn-danger delete-confirm-button ajax_link">{{ trans('messages.delete') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->
	
	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#link_confirm_model">Open modal</a>
	<!-- Basic modal -->
	<div id="link_confirm_model" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="confirm-link-form" onkeypress="return event.keyCode != 13;">
					<div class="modal-header bg-info-800">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2 class="modal-title">{{ trans('messages.are_you_sure') }}</h2>
					</div>
	
					<div class="modal-body">
						
							<h6></h6>
						
					</div>
	
					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn bg-info-800 link-confirm-button ajax_link">{{ trans('messages.confirm') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->
	
	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#list_delete_confirm_model">aa</a>
	<!-- Basic modal -->
	<div id="list_delete_confirm_model" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="list-confirm-delete-form" onkeypress="return event.keyCode != 13;">
					<div class="modal-header bg-danger">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2 class="modal-title">{{ trans('messages.are_you_sure') }}</h2>
					</div>
	
					<div class="modal-body">
						
							<div class="content">
								
							</div>
							
							<div class="form-group">
								<label>{!! trans('messages.type_delete_to_confirm') !!}</label>
								<input class="form-control" name="delete" />
							</div>
						
					</div>
	
					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn btn-danger list-delete-confirm-button ajax_link">{{ trans('messages.delete') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->
</body>
</html>
