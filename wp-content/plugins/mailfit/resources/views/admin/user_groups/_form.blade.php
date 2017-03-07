                    <div class="sub_section">
						<div class="row">
							<div class="col-md-12">									

									@include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $group->name, 'help_class' => 'user_group', 'rules' => Acelle\Model\UserGroup::$rules])

							</div>
						</div>
						<div class="row">
							<div class="col-md-6">									
								<div class="form-group checkbox-right-switch">
									@include('helpers.form_control', [
										'type' => 'checkbox',
										'name' => 'frontend_access',
										'value' => $group->frontend_access,
										'help_class' => 'user_group',
										'options' => [false, true],
										'rules' => Acelle\Model\UserGroup::rules()
									])
								</div>
							</div>
							<div class="col-md-6">									
								<div class="form-group checkbox-right-switch">
									@include('helpers.form_control', [
										'type' => 'checkbox',
										'name' => 'backend_access',
										'value' => $group->backend_access,
										'help_class' => 'user_group',
										'options' => [false, true],
										'rules' => Acelle\Model\UserGroup::rules()
									])
								</div>
							</div>
						</div>
					</div>
					
					<div class="options-container">
						<h2><i class="icon-gear"></i> {{ trans('messages.user_group_options') }}</h2>
							
						<div class="tabbable">
							<ul class="nav nav-tabs nav-tabs-top">
								<li class="active text-semibold frontend-box"><a href="#top-tab1" data-toggle="tab">
									<i class="icon-user"></i> {{ trans('messages.frontend') }}</a></li>
								<li class="text-semibold backend-box"><a href="#top-tab2" data-toggle="tab">
									<i class="icon-user-tie"></i> {{ trans('messages.backend') }}</a></li>
							</ul>
	
							<div class="tab-content">
								<div class="tab-pane active frontend-box" id="top-tab1">
									<h3 class="text-teal-800">{{ trans('messages.list_or_subscriber_or_segment_or_campaign') }}</h3>
									<div class="row">
										<div class="boxing col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][list_max]',
												'value' => $options['frontend']['list_max'],
												'label' => trans('messages.max_lists'),
												'help_class' => 'user_group',
												'options' => ['true', 'false'],
												'rules' => Acelle\Model\UserGroup::rules()
											])
											<div class="checkbox inline unlimited-check text-semibold">
												<label>
													<input{{ $options['frontend']['list_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
													{{ trans('messages.unlimited') }}
												</label>
											</div>
										</div>
										<div class="boxing col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][subscriber_max]',
												'value' => $options['frontend']['subscriber_max'],
												'label' => trans('messages.max_subscribers'),
												'help_class' => 'user_group',
												'options' => ['true', 'false'],
												'rules' => Acelle\Model\UserGroup::rules()
											])
											<div class="checkbox inline unlimited-check text-semibold">
												<label>
													<input{{ $options['frontend']['subscriber_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
													{{ trans('messages.unlimited') }}
												</label>
											</div>
										</div>
										<div class="boxing col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][subscriber_per_list_max]',
												'value' => $options['frontend']['subscriber_per_list_max'],
												'label' => trans('messages.max_subscribers_per_list'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
											<div class="checkbox inline unlimited-check text-semibold">
												<label>
													<input{{ $options['frontend']['subscriber_per_list_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
													{{ trans('messages.unlimited') }}
												</label>
											</div>
										</div>
										<div class="boxing col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][segment_per_list_max]',
												'value' => $options['frontend']['segment_per_list_max'],
												'label' => trans('messages.segment_per_list_max'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
											<div class="checkbox inline unlimited-check text-semibold">
												<label>
													<input{{ $options['frontend']['segment_per_list_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
													{{ trans('messages.unlimited') }}
												</label>
											</div>
										</div>										
									</div>
									<div class="row">
										<div class="boxing col-md-3">
											@include('helpers.form_control', ['type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][campaign_max]',
												'value' => $options['frontend']['campaign_max'],
												'label' => trans('messages.max_campaigns'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
											<div class="checkbox inline unlimited-check text-semibold">
												<label>
													<input{{ $options['frontend']['campaign_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
													{{ trans('messages.unlimited') }}
												</label>
											</div>
																		
										</div>
										<div class="boxing col-md-3">
											<label class="text-semibold">{{ trans('messages.unsubscribe_url_required') }} <span class="text-danger">*</span></label>
											<br />
											<span class="notoping">
												@include('helpers.form_control', ['type' => 'checkbox',
													'class' => '',
													'name' => 'options[frontend][unsubscribe_url_required]',
													'value' => $options['frontend']['unsubscribe_url_required'],
													'label' => '',
													'options' => ['no','yes'],
													'help_class' => 'user_group',
													'rules' => Acelle\Model\UserGroup::rules()
												])
											</span>
										</div>
									</div>
										
									<h3 class="text-teal-800">{{ trans('messages.file_upload') }}</h3>
									<div class="row">
										<div class="boxing col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][max_size_upload_total]',
												'value' => $options['frontend']['max_size_upload_total'],
												'label' => trans('messages.max_size_upload_total'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
										</div>
										<div class="boxing col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][max_file_size_upload]',
												'value' => $options['frontend']['max_file_size_upload'],
												'label' => trans('messages.max_file_size_upload'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
										</div>		
									</div>
									
										
									<h3 class="text-teal-800">{{ trans('messages.sending_quota') }}</h3>
									<div class="row">
										<div class="boxing col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][sending_quota]',
												'value' => $options['frontend']['sending_quota'],
												'label' => trans('messages.sending_quota'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
											<div class="checkbox inline sending_quota-check text-semibold">
												<label>
													<input{{ $options['frontend']['sending_quota']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
													{{ trans('messages.unlimited') }}
												</label>
											</div>
										</div>
										<div class="col-md-3">
											@include('helpers.form_control', [
												'type' => 'text',
												'class' => 'numeric',
												'name' => 'options[frontend][sending_quota_time]',
												'value' => $options['frontend']['sending_quota_time'],
												'label' => trans('messages.quota_time'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
										</div>
										<div class="col-md-3">
											@include('helpers.form_control', ['type' => 'select',
												'name' => 'options[frontend][sending_quota_time_unit]',
												'value' => $options['frontend']['sending_quota_time_unit'],
												'label' => trans('messages.quota_time_unit'),
												'options' => Acelle\Model\UserGroup::timeUnitOptions(),
												'include_blank' => trans('messages.choose'),
												'help_class' => 'user_group',
												'rules' => Acelle\Model\UserGroup::rules()
											])
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="mt-0 mb-5 text-semibold">{{ trans('messages.max_number_of_processes') }}</label>
												@include('helpers.form_control', ['type' => 'select',
													'name' => 'options[frontend][max_process]',
													'value' => $options['frontend']['max_process'],
													'label' => '',
													'options' => Acelle\Model\UserGroup::multiProcessSelectOptions(),
													'help_class' => 'user_group',
													'rules' => Acelle\Model\UserGroup::rules()
												])
											</div>
										</div>
									</div>
										
									<h3 class="text-teal-800">{{ trans('messages.sending_servers') }}</h3>
									<div class="row">
										<div class="col-md-3">
											{{ trans('messages.all_sending_servers') }}&nbsp;&nbsp;&nbsp;
											<span class="notoping">												
												@include('helpers.form_control', ['type' => 'checkbox',
													'class' => '',
													'name' => 'options[frontend][all_sending_servers]',
													'value' => $options['frontend']['all_sending_servers'],
													'label' => '',
													'options' => ['no','yes'],
													'help_class' => 'user_group',
													'rules' => Acelle\Model\UserGroup::rules()
												])
											</span>
											
										</div>
									</div>
									<br />
									<div class="row sending-servers">
										@foreach (Acelle\Model\SendingServer::getAll()->orderBy("name")->get() as $server)
																					
											<div class="col-md-6">
												<h5 class="mt-0 mb-5 text-semibold text-teal-600">{{ $server->name }}</h5>
												<div class="row">											
													<div class="col-md-2">
														<div class="form-group">
															<label class="mt-0 mb-5 text-semibold">{{ trans('messages.choose') }}</label>
															@include('helpers.form_control', [
																'type' => 'checkbox',
																'name' => 'sending_servers[' . $server->uid . '][check]',
																'value' => $group->user_group_sending_servers->contains('sending_server_id', $server->id),
																'label' => '',
																'options' => [false, true],
																'help_class' => 'user_group',
																'rules' => Acelle\Model\UserGroup::rules()
															])
														</div>
														<br><br>
													</div>
													<div class="col-md-9">
														@include('helpers.form_control', [
															'type' => 'text',
															'class' => 'numeric',
															'name' => 'sending_servers[' . $server->uid . '][fitness]',
															'label' => trans('messages.fitness'),
															'value' => (is_object($group->user_group_sending_servers()->where('sending_server_id', $server->id)->first()) ? $group->user_group_sending_servers()->where('sending_server_id', $server->id)->first()->fitness : "100"),
															'help_class' => 'user_group',
															'rules' => Acelle\Model\UserGroup::rules()
														])
													</div>
												</div>
											</div>
												
										@endforeach
									</div>
										
								</div>
								
								<div class="tab-pane backend-box" id="top-tab2">
									
									@foreach (Acelle\Model\UserGroup::backendPermissions() as $key => $items)
										<h3 class="text-teal-800">{{ trans('messages.' . $key) }}</h3>
										<div class="row">
											@foreach ($items as $act => $ops)
												<div class="col-md-3">
													@if (count($ops["options"]) > 2)
														@include('helpers.form_control', [
															'type' => 'select',
															'class' => 'numeric',
															'name' => 'options[backend][' . $key . "_" . $act .']',
															'value' => $options['backend'][$key . "_" . $act],
															'label' => trans('messages.' . $act),
															'options' => $ops["options"],
															'help_class' => 'user_group',
															'rules' => Acelle\Model\UserGroup::rules()
														])
													@else
														<div class="checkbox-box-group">
															@include('helpers.form_control', ['type' => 'checkbox',
																'class' => 'numeric',
																'name' => 'options[backend][' . $key . "_" . $act .']',
																'value' => $options['backend'][$key . "_" . $act],
																'label' => trans('messages.' . $act),
																'options' => ['no','yes'],
																'help_class' => 'user_group',
																'rules' => Acelle\Model\UserGroup::rules()
															])
														</div>
													@endif
												</div>
											@endforeach
										</div>
									@endforeach
									
								</div>
	
							</div>
						</div>
					</div>
					
					@if ($group->users()->count() && false)						
						<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#group_check_confirm_model">aa</a>
						<!-- Basic modal -->
						<div id="group_check_confirm_model" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<form class="" onkeypress="return event.keyCode != 13;">
										<div class="modal-header bg-danger">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h2 class="modal-title">{{ trans('messages.are_you_sure') }}</h2>
										</div>
						
										<div class="modal-body">
											
											<div class="alert alert-danger text-semibold">
												{{ trans('messages.uncheck_frontend_confirm') }}
											</div>
											
											<div class="content">
												<ul class="modern-listing">
													@foreach ($group->users as $user)
														<li>
															<i class="icon-cancel-circle2 text-danger"></i>
															<h4 class="text-danger">{{ $user->displayName() }}</h4>
															<p>
																@if ($user->subscribers()->count())
																	<span class="text-bold text-danger">{{ $user->subscribers()->count() }}</span> {{ trans('messages.subscribers') }}<pp>,</pp>
																@endif
																@if ($user->campaigns()->count())
																	<span class="text-bold text-danger">{{ $user->campaigns()->count() }}</span> {{ trans('messages.campaigns') }}<pp>,</pp>
																@endif
																@if ($user->lists()->count())
																	<span class="text-bold text-danger">{{ $user->lists()->count() }}</span> {{ trans('messages.lists') }}<pp>,</pp>
																@endif
															</p>                        
														</li>
													@endforeach
												</ul>
											</div>
												
										</div>
						
										<div class="modal-footer">
											<button type="button" class="btn btn-link cancel-frontend-uncheck" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
											<a class="btn btn-danger" data-dismiss="modal">{{ trans('messages.confirm') }}</a>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!-- /basic modal -->
						
						
					@endif
					
					<script>
						$(document).ready(function() {
							////// add segment condition
							////$(document).on("change", "input[name=frontend_access]", function(e) {
							////	if(!$(this).is(":checked")) {
							////		$('a[data-target="#group_check_confirm_model"]').trigger("click");
							////	}
							////});
							////
							////$(document).on("click", ".cancel-frontend-uncheck", function(e) {
							////	$("input[name=frontend_access]").last().next().click();
							////});
							
							// all sending servers checking
							$(document).on("change", "input[name='options[frontend][all_sending_servers]']", function(e) {
								if($("input[name='options[frontend][all_sending_servers]']:checked").length) {									
									$(".sending-servers").find("input[type=checkbox]").each(function() {
										if($(this).is(":checked")) {
											$(this).parents(".form-group").find(".switchery").eq(1).click();
										}
									});
									$(".sending-servers").hide();
								} else {
									$(".sending-servers").show();
								}
							});
							
							$("input[name='options[frontend][all_sending_servers]']").trigger("change");
						});
					</script>
					