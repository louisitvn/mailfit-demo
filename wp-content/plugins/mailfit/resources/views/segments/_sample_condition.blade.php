                                <div class="row condition-line" rel="__index__">
									<div class="col-md-4">
										<div class="form-group">
											<select class="select" name="conditions[__index__][field_id]">
												<optgroup label="{{ trans('messages.list_fields') }}">
													@foreach($list->getFields as $field)
														<option value="{{ $field->uid }}">{{ $field->label }}</option>
													@endforeach
												</optgroup>
											</select>
										</div>
									</div>
									<div class="col-md-3 operator-col">
										@include('helpers.form_control', ['type' => 'select', 'name' => 'conditions[__index__][operator]', 'label' => '', 'value' => '', 'options' => Acelle\Model\Segment::operators()])
									</div>
									<div class="col-md-4 value-col">
										@include('helpers.form_control', ['type' => 'text', 'name' => 'conditions[__index__][value]', 'label' => '', 'value' => ''])
									</div>
									<div class="col-md-1">
										<a onclick="$(this).parents('.condition-line').remove()" href="#delete" class="btn bg-danger-400"><i class="icon-trash"></i></a>
									</div>
								</div>