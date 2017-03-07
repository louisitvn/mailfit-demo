@extends('layouts.frontend')

@section('title', $list->name . ": " . trans('messages.subscribers'))

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
				
				<h2 class="text-bold text-teal-800 mb-10"><i class="icon-users4"></i> {{ trans('messages.subscribers') }}</h2>

				<form class="listing-form"
					data-url="{{ action('SubscriberController@listing', $list->uid) }}"
					per-page="{{ Acelle\Model\Subscriber::$itemsPerPage }}"					
				>				
					<div class="row top-list-controls">
						<div class="col-md-10">
							@if ($subscribers->count() >= 0)					
								<div class="filter-box">
									<div class="btn-group list_actions hide mr-10">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.actions') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">											
											<li>
												<a link-confirm="{{ trans('messages.subscribe_subscribers_confirm') }}" href="{{ action('SubscriberController@subscribe', $list->uid) }}">
													<i class="icon-enter"></i> {{ trans('messages.subscribe') }}
												</a>
											</li>
											<li>
												<a link-confirm="{{ trans('messages.unsubscribe_subscribers_confirm') }}" href="{{ action('SubscriberController@unsubscribe', $list->uid) }}">
													<i class="icon-exit"></i> {{ trans('messages.unsubscribe') }}
												</a>
											</li>
											<li>
												<a href="#">
													<i class="icon-copy4"></i> {{ trans('messages.copy_to') }}
													<i class="icon-arrow-right13 pull-right"></i>
												</a>
												<ul>
													<li>
														<a>{{ trans('messages.replace_if_exist') }} <i class="icon-arrow-right13 pull-right"></i></a>
														<ul>
															@forelse ($list->otherLists() as $l)
																<li>
																	<a link-confirm="{{ trans('messages.subscribers_copy_confirm.replace') }}" href="{{ action('SubscriberController@copy', ['list_uid' => $l->uid, 'type' => 'update']) }}">
																		<i class="icon-address-book2"></i> {{ $l->name }}
																	</a>
																</li>
															@empty
																<li><a href="#">({{ trans('messages.empty') }})</a></li>
															@endforelse
														</ul>
													</li>
													<li>
														<a>{{ trans('messages.keep_if_exist') }} <i class="icon-arrow-right13 pull-right"></i></a>
														<ul>
															@forelse ($list->otherLists() as $l)
																<li>
																	<a link-confirm="{{ trans('messages.subscribers_copy_confirm.keep') }}" href="{{ action('SubscriberController@copy', ['list_uid' => $l->uid, 'type' => 'keep']) }}">
																		<i class="icon-address-book2"></i> {{ $l->name }}
																	</a>
																</li>
															@empty
																<li><a href="#">({{ trans('messages.empty') }})</a></li>
															@endforelse
														</ul>
													</li>
												</ul>												
											</li>
											<li>
												<a href="#">
													<i class="icon-move-right"></i> {{ trans('messages.move_to') }}
													<i class="icon-arrow-right13 pull-right"></i>
												</a>
												<ul>
													<li>
														<a>{{ trans('messages.replace_if_exist') }} <i class="icon-arrow-right13 pull-right"></i></a>
														<ul>
															@forelse ($list->otherLists() as $l)
																<li>
																	<a link-confirm="{{ trans('messages.subscribers_move_confirm.replace') }}" href="{{ action('SubscriberController@move', ['list_uid' => $l->uid, 'type' => 'update']) }}">
																		<i class="icon-address-book2"></i> {{ $l->name }}
																	</a>
																</li>
															@empty
																<li><a href="#">({{ trans('messages.empty') }})</a></li>
															@endforelse
														</ul>
													</li>
													<li>
														<a>{{ trans('messages.keep_if_exist') }} <i class="icon-arrow-right13 pull-right"></i></a>
														<ul>
															@forelse ($list->otherLists() as $l)
																<li>
																	<a link-confirm="{{ trans('messages.subscribers_move_confirm.keep') }}" href="{{ action('SubscriberController@move', ['list_uid' => $l->uid, 'type' => 'keep']) }}">
																		<i class="icon-address-book2"></i> {{ $l->name }}
																	</a>
																</li>
															@empty
																<li><a href="#">({{ trans('messages.empty') }})</a></li>
															@endforelse
														</ul>
													</li>
												</ul>												
											</li>
											<li>
												<a delete-confirm="{{ trans('messages.delete_subscribers_confirm') }}" href="{{ action('SubscriberController@delete', $list->uid) }}">
													<i class="icon-trash"></i> {{ trans('messages.delete') }}
												</a>
											</li>
										</ul>
									</div>
									<div class="checkbox inline check_all_list">
										<label>
											<input type="checkbox" class="styled check_all">
										</label>
									</div>				
									<span class="filter-group">
										<span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
										<select class="select" name="sort-order">											
											<option value="subscribers.created_at">{{ trans('messages.created_at') }}</option>
											<option value="subscribers.updated_at">{{ trans('messages.updated_at') }}</option>
											<option value="subscribers.email">{{ trans('messages.email') }}</option>
										</select>										
										<button class="btn btn-xs sort-direction" rel="desc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
											<i class="icon-sort-amount-desc"></i>
										</button>
									</span>
									<span class="">										
										<select class="select" name="status">
											<option value="">{{ trans('messages.all_subscribers') }}</option>
											<option value="subscribed">{{ trans('messages.subscribed') }}</option>
											<option value="unsubscribed">{{ trans('messages.unsubscribed') }}</option>
											<option value="unconfirmed">{{ trans('messages.unconfirmed') }}</option>
											<option value="spam-reported">{{ trans('messages.spam-reported') }}</option>
											<option value="blacklisted">{{ trans('messages.blacklisted') }}</option>
										</select>										
									</span>
									<div class="btn-group list_columns ml-10">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.columns') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu dropdown-menu-right">
											@foreach ($list->getFields as $field)
												@if ($field->tag != "EMAIL")
													<li>
														<div class="checkbox">
															<label>
																<input {{ ($field->required ? "checked='checked'" : "") }} type="checkbox" id="{{ $field->tag }}" name="columns[]" value="{{ $field->uid }}" class="styled">
																{{ $field->label }}
															</label>
														</div>															
													</li>	
												@endif
											@endforeach
											<li>
												<div class="checkbox">
													<label>
														<input checked="checked" type="checkbox" id="created_at" name="columns[]" value="created_at" class="styled">
														{{ trans('messages.created_at') }}
													</label>
												</div>															
											</li>
											<li>
												<div class="checkbox">
													<label>
														<input checked="checked" type="checkbox" id="updated_at" name="columns[]" value="updated_at" class="styled">
														{{ trans('messages.updated_at') }}
													</label>
												</div>															
											</li>
										</ul>
									</div>
									<span class="text-nowrap">
										<input name="search_keyword" class="form-control search" placeholder="{{ trans('messages.type_to_search') }}" />
										<i class="icon-search4 keyword_search_button"></i>
									</span>
								</div>
							@endif
						</div>
						<div class="col-md-2 text-right">
							<a href="{{ action("SubscriberController@create", $list->uid) }}" type="button" class="btn bg-info-800">
								<i class="icon icon-plus2"></i> {{ trans('messages.create_subscriber') }}
							</a>
						</div>
					</div>
					
					<div class="pml-table-container">
						
						
						
					</div>
				</form>
@endsection
