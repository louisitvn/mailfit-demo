          <div class="sub_section">
						<h2 class="text-semibold text-teal-800">{{ trans('messages.list_details') }}</h2>
							
						<div class="row">
							<div class="col-md-6">
										
									@include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $list->name, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])									
									
							</div>
							<div class="col-md-6">
									
									@include('helpers.form_control', ['type' => 'text', 'name' => 'from_email', 'label' => trans('messages.default_from_email_address'), 'value' => $list->from_email, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							
						</div>
							
						<div class="row">
							<div class="col-md-6">									
									
									@include('helpers.form_control', ['type' => 'text', 'name' => 'from_name', 'label' => trans('messages.default_from_name'), 'value' => $list->from_name, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							<div class="col-md-6">
										
									@include('helpers.form_control', ['type' => 'text', 'name' => 'default_subject', 'label' => trans('messages.default_email_subject'), 'value' => $list->default_subject, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							
						</div>
					</div>
					
					<div class="sub_section">
						<h2 class="text-semibold text-teal-800">
							{{ trans('messages.contact_information') }}
						</h2>
							
						<div class="row">
							<div class="col-md-6">									
									
									@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[company]', 'label' => trans('messages.company_organization'), 'value' => $list->contact->company, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])

							</div>
							<div class="col-md-6">
										
									@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[state]', 'label' => trans('messages.state_province_region'), 'value' => $list->contact->state, 'rules' => Acelle\Model\MailList::$rules])						
									
							</div>
							
						</div>
							
							<div class="row">
							<div class="col-md-6">									
									
									@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[address_1]', 'label' => trans('messages.address_1'), 'value' => $list->contact->address_1, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							<div class="col-md-6">
										
									@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[city]', 'label' => trans('messages.city'), 'value' => $list->contact->city, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							
						</div>
							
						<div class="row">
							<div class="col-md-6">									
									
									@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[address_2]', 'label' => trans('messages.address_2'), 'value' => $list->contact->address_2, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							<div class="col-md-6">
										
									@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[zip]', 'label' => trans('messages.zip_postal_code'), 'value' => $list->contact->zip, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							
						</div>
							
						<div class="row">
							<div class="col-md-6">									
									
									@include('helpers.form_control', ['type' => 'select', 'name' => 'contact[country_id]', 'label' => trans('messages.country'), 'value' => $list->contact->country_id, 'options' => Acelle\Model\Country::getSelectOptions(), 'include_blank' => trans('messages.choose'), 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							<div class="col-md-6">
										
									@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[phone]', 'label' => trans('messages.phone'), 'value' => $list->contact->phone, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
									
							</div>
							
						</div>
							
							
						<div class="row">
							<div class="col-md-6">
							
								@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[email]', 'label' => trans('messages.email'), 'value' => $list->contact->email, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
								
							</div>
							<div class="col-md-6">
							
								@include('helpers.form_control', ['type' => 'text', 'name' => 'contact[url]', 'label' => trans('messages.url'), 'label' => trans('messages.home_page'), 'value' => $list->contact->url, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
								
							</div>
						</div>
					</div>		
						
					<div class="sub_section">
						<h2 class="text-semibold text-teal-800">{{ trans('messages.settings') }}</h2>
						<div class="row">
							<div class="col-md-6 hide">
							
								@include('helpers.form_control', ['type' => 'text', 'name' => 'email_subscribe', 'value' => $list->email_subscribe, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
								
								@include('helpers.form_control', ['type' => 'text', 'name' => 'email_unsubscribe', 'value' => $list->email_unsubscribe, 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
								
								<br />									
									
							</div>
							<div class="col-md-6">
								<div class="form-group checkbox-right-switch">
									@include('helpers.form_control', [
										'type' => 'checkbox',
										'name' => 'subscribe_confirmation',
										'value' => $list->subscribe_confirmation,
										'options' => [false,true],
										'help_class' => 'list',
										'rules' => Acelle\Model\MailList::$rules
									])
									
									@include('helpers.form_control', ['type' => 'checkbox', 'name' => 'unsubscribe_notification', 'value' => $list->unsubscribe_notification, 'options' => [false,true], 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
								</div>
							</div>
							<div class="col-md-6">
									<div class="form-group checkbox-right-switch">
									
										@include('helpers.form_control', ['type' => 'checkbox', 'name' => 'send_welcome_email', 'value' => $list->send_welcome_email, 'options' => [false,true], 'help_class' => 'list', 'rules' => Acelle\Model\MailList::$rules])
										
									</div>
							</div>
							
						</div>
					</div>