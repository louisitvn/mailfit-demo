@extends('layouts.frontend')

@section('title', $list->name . ": " . trans('messages.import'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
		
	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>		
@endsection

@section('page_header')

			@include("lists._header")

@endsection

@section('content')
				
				@include("lists._menu")
				
				<h2 class="text-bold text-teal-800"><i class="icon-users4"></i> {{ trans('messages.import_from_wrodpress_users') }}</h2>
				
                <div class="row">
                    <div class="col-md-12">
                        <form
                            process-url="{{ action('MailListController@wordpressImportProccess', $list->uid) }}"
                            action="{{ action('MailListController@wordpressImport', $list->uid) }}" method="POST" class="ajax_upload_form form-validate-jquery">
                            {{ csrf_field() }}
                            
							<div class="alert alert-info">
								{!! trans('messages.import_from_wordpress_file_help') !!}
							</div>
							
							
                            <div class="upload_file before">
								<div class="row">
									<div class="col-md-6">
										<div class="">
											@include('helpers.form_control', [
												'required' => true,
												'type' => 'checkboxes',
												'label' => trans('messages.choose_wordpress_user_group_for_import'),
												'name' => 'wp_roles[]',
												'options' => Acelle\Model\User::getWPUserRoleSelectOptions(),
												'value' => 'yes',
											])
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="checkbox-one-line">
											@include('helpers.form_control', [
												'required' => true,
												'type' => 'checkbox',
												'label' => trans('messages.update_subscriber_information_if_email_exists'),
												'name' => 'update_exists',
												'options' => ['no', 'yes'],
												'value' => 'yes',
											])
										</div>
									</div>
								</div>
									
                                <br />
                                <div class="text-left">
                                    <button class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.import') }}</button>
                                    <a href="{{ action('SubscriberController@index', $list->uid) }}" class="btn bg-grey-800"><i class="icon-cross2"></i> {{ trans('messages.cancel') }}</a>
                                </div>
                                <br />
                            </div>
                            
                            <div class="form-group processing hide">
								<h4>{{ trans('messages.please_wait_import') }}</h4>
                                <div class="progress progress-lg">
									<div class="progress-bar progress-error progress-bar-danger" style="width: 0%">
										<span><span class="number">0</span>% {{ trans('messages.error') }}</span>
									</div>

                                    <div class="progress-bar progress-total active" style="width: 0%">
                                        <span><span class="number">0</span>% {{ trans('messages.complete') }}</span>
                                    </div>
                                </div>
								<label></label>
								<a data-method="POST" link-confirm="{{ trans('messages.cancel_system_jobs_confirm') }}" data-href="{{ action('SystemJobController@cancel', ["uids" => ""]) }}" type="button" class="btn bg-grey btn-icon cancel processing">
									{{ trans('messages.cancel') }}
								</a>
                            </div>
								
							<div class="form-group finish hide">
                                <div class="text-left">
                                    <a href="#retry" class="btn bg-grey-800 mr-10 retry"><i class="icon-enter5"></i> {{ trans('messages.back') }}</a>
                                </div>
                            </div>
                            
                        </form>  
                        
                    </div>
                </div>
                      
				<script>
					var current_list_uid = '{{ $list->uid }}';
					
					function check_process(first) {
						var form = $("form.ajax_upload_form")
						var url = form.attr("process-url")
						var bar = form.find('.progress-total');
						var bar_s = form.find('.progress-success');
						var bar_e = form.find('.progress-error');
						
						$.ajax({
							url : url + "?&current_list_uid="+current_list_uid,
							type: "GET",
							success:function(result, textStatus, jqXHR)
							{
								if(result != "none" && result.job.status != "cancelled") {
									// Update cancel link
									form.find('.cancel').attr('href', form.find('.cancel').attr('data-href') + result.job.id);
									
									// update progress bar
									var total = parseFloat(result.data.total);
									var success = parseFloat(result.data.success);
									var error = parseFloat(result.data.error);
									
									if (typeof(first) == "undefined") {
										form.find(".processing label").html(result.data.message);
										bar.find(".number").html(Math.round(success/total*100));
										bar.css({
											width: (success/total*100) + '%'
										});	
										bar_e.find(".number").html(Math.round(error/total*100));
										bar_e.css({
											width: (error/total*100) + '%'
										});
										
										if (result.data.status == "failed" && typeof(first) == "undefined") {
											form.find('.finish').removeClass("hide");
											form.find('.before').addClass("hide");
											form.find(".processing").removeClass('hide');
											form.find('.success').addClass("hide");
										}
										
										if (result.data.status == "done" && typeof(first) == "undefined") {
											form.find('.upload_file .progress-bar').addClass('success');										
											form.find(".before").addClass('hide');
											form.find(".processing").removeClass('hide');
											form.find('.finish').removeClass('hide');
											form.find('.success').removeClass("hide");
											
											form.find(".processing h4").html('{{ trans('messages.import_completed') }}');
											
											if(!form.find('.finish').hasClass('hide')) {
												// Success alert
												swal({
													title: '{{ trans('messages.import_completed') }}',
													text: "",
													confirmButtonColor: "#00695C",
													type: "success",
													allowOutsideClick: true,
													confirmButtonText: LANG_OK,
												});
											}
										}
									}
									
									if (result.data.status == "running" || result.data.status == "new") {
										setTimeout("check_process()", 1000);
										form.find(".before").addClass('hide');
										form.find(".processing").removeClass('hide');
										form.find('.finish').addClass('hide');
									}
								}
								
								if (result.job.status == "cancelled") {
									form.find('.retry').trigger('click');
								}
							}
						})
					}						
					
					$(document).on("submit", "form.ajax_upload_form", function() {
						
						if(!$('input[name="wp_roles[]"]:checked').length) {
							alert("{{ trans('messages.please_choose_wp_uers_groups') }}");
							
							return false;
						}
					
						var form = $(this);
						setTimeout("check_process()", 2000);
						if(!form.valid()) {
							$("label.error").insertAfter(".uploader");
							return false;
						}
						
						var formData = new FormData($(this)[0]);
						var url = form.attr('action');
						var bar = form.find('.progress-total');
						var bar_s = form.find('.progress-success');
						var bar_e = form.find('.progress-error');
						
						// return to 0
						bar_s.find(".number").html(parseInt(0));
						bar_s.css({
							width: 0 + '%'
						});
						bar_e.find(".number").html(parseInt(0));
						bar_e.css({
							width: 0 + '%'
						});
						
						form.find(".processing h4").html('{{ trans('messages.please_wait_import') }}');
						
						$(".processing label").html("{{ trans('messages.uploading') }}");
						$(".before").addClass('hide');
						$(".processing").removeClass('hide');
						
						$.ajax({								
							url: url,
							type: 'POST',
							data: formData,
							success: function (data) {
								check_process();
								
								if(data == 'max_file_upload') {
									// Success alert
									swal({
										title: '{{ trans('messages.file_import_to_large') }}',
										text: "",
										confirmButtonColor: "#00695C",
										type: "error",
										allowOutsideClick: true,
										confirmButtonText: LANG_OK,
									});
									
									form.find('.retry').trigger('click');
									return;
								}									
							},
							cache: false,
							contentType: false,
							processData: false
						});
						
						return false;
					});
					
					$(document).on("click", ".retry", function() {
						$(".input[type=file]").val("");
						$(".finish").addClass('hide');
						$(".processing").addClass('hide');
						$(".before").removeClass('hide');
						var bars = $(".progress-bar");
						bars.find(".number").html(parseInt(0));
						bars.css({
							width: 0 + '%'
						});
					});
					
					$(document).ready(function() {
						check_process(true);
					});
				</script>
					
				@include("lists._wordpress_import")
			
@endsection
