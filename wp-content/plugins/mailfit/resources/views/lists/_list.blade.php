                        @if ($lists->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($lists as $key => $item)
									<tr>
										<td width="1%">
											<div class="text-nowrap">
												<div class="checkbox inline">
													<label>
														<input type="checkbox" class="node styled"
															custom-order="{{ $item->custom_order }}"
															name="ids[]"
															value="{{ $item->uid }}"
														/>
													</label>
												</div>
												@if (request()->sort_order == 'custom_order' && empty(request()->keyword))
													<i data-action="move" class="icon icon-more2 list-drag-button"></i>
												@endif
											</div>
										</td>
										<td>
											<h5 class="no-margin text-bold">
												<a class="kq_search" href="{{ action('MailListController@overview', ['uid' => $item->uid]) }}">{{ $item->name }}</a>
											</h5>
											<span class="text-muted">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
										</td>
										<td class="stat-fix-size-sm">
											<div class="single-stat-box pull-left">
												<a href="{{ action('SubscriberController@index', $item->uid) }}">
													<span class="no-margin stat-num">{{ $item->subscribers()->count() }}</span>
												</a>
												<br />
												<span class="text-muted">{{ trans("messages." . Acelle\Library\Tool::getPluralPrase('subscriber', $item->subscribers()->count())) }}</span>
											</div>
											<div class="single-stat-box pull-left ml-20">
												<span class="no-margin text-teal-800 stat-num">{{ $item->openUniqRate() }}%</span>
												<div class="progress progress-xxs">
													<div class="progress-bar progress-bar-info" style="width: {{ $item->openUniqRate() }}%">
													</div>
												</div>
												<span class="text-muted">{{ trans('messages.open_rate') }}</span>
											</div>
											<div class="single-stat-box pull-left ml-20">
												<span class="no-margin text-teal-800 stat-num">{{ $item->clickRate() }}%</span>
												<div class="progress progress-xxs">
													<div class="progress-bar progress-bar-info" style="width: {{ $item->clickRate() }}%">
													</div>
												</div>
												<span class="text-muted">{{ trans('messages.click_rate') }}</span>
											</div>
											<br style="clear:both" />
										</td>
										<td class="text-right">
											<span class="text-muted2 list-status pull-left">
												<span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.list_status_' . $item->status) }}</span>
											</span>
											<a href="{{ action('SubscriberController@create', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.create_subscriber') }}" type="button" class="btn bg-grey-600 btn-icon"><i class="icon icon-plus2"></i><i class="glyphicon glyphicon-user"></i></a>
											<a href="{{ action('MailListController@wordpressImport', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.wordpress_import') }} " type="button" class="btn bg-brown btn-icon">
												<i class="icon-wordpress"></i> <i class="icon icon-download"></i> 
											</a>
											<a href="{{ action('MailListController@overview', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.overview') }}" type="button" class="btn bg-teal-600 btn-icon"><i class="icon-stats-growth mr-0 pr-0"></i></a>
											<div class="btn-group">										
												<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
												<ul class="dropdown-menu dropdown-menu-right">
													<li><a href="{{ action('SubscriberController@index', $item->uid) }}"><i class="icon-users4"></i> {{ trans("messages.subscribers") }}</a></li>
													<li><a href="{{ action('SegmentController@index', $item->uid) }}"><i class="icon-make-group"></i> {{ trans('messages.segments') }}</a></li>
													<li>
														<a href="{{ action('MailListController@embeddedForm', $item->uid) }}">
															<i class="icon-embed2"></i> {{ trans('messages.Embedded_form') }}
														</a>
													</li>
													<li><a href="{{ action('PageController@update', ['list_uid' => $item->uid, 'alias' => 'sign_up_form']) }}"><i class="icon-certificate"></i> {{ trans('messages.custom_forms_and_emails') }}</a></li>
													<li>
														<a class="level-1" href="{{ action('FieldController@index', $item->uid) }}">
															<i class="icon-list3"></i> {{ trans('messages.manage_list_fields') }}
														</a>
													</li>
													<li><a href="{{ action('MailListController@edit', $item->uid) }}"><i class="icon-pencil7"></i> {{ trans("messages.edit_list") }}</a></li>
													<li>
														<a class="level-1" href="{{ action('FieldController@index', $item->uid) }}">
															<i class="icon-list3"></i> {{ trans('messages.manage_list_fields') }}
														</a>
													</li>		
													<li class='divider'></li>
													<li><a href="{{ action('SubscriberController@import', $item->uid) }}"><i class="icon-download4"></i> {{ trans('messages.import') }}</a></li>
													<li>
														<a href="{{ action('MailListController@wordpressImport', $item->uid) }}">
															<i class="icon-download4"></i> <i class="icon-wordpress mr-0"></i> {{ trans('messages.wordpress_import') }} 
														</a>
													</li>
													<li><a href="{{ action('SubscriberController@export', $item->uid) }}"><i class="icon-upload4"></i> {{ trans('messages.export') }}</a></li>
													<li class='divider'></li>
													<li>														
														<a data-uid="{{ $item->uid }}" data-name="{{ trans("messages.copy_of_list", ['name' => $item->name]) }}" class="copy-list-link">
															<i class="icon-copy4"></i> {{ trans('messages.copy') }}
														</a>
													</li>
													<li>														
														<a list-delete-confirm="{{ action('MailListController@deleteConfirm', ['uids' => $item->uid]) }}" href="{{ action('MailListController@delete', ['uids' => $item->uid]) }}">
															<i class="icon-trash"></i> {{ trans('messages.delete') }}
														</a>
													</li>													
												</ul>
											</div>
										</td>
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select', ["items" => $lists])
							{{ $lists->links() }}
						@elseif (!empty(request()->keyword))
							<div class="empty-list">
								<i class="icon-address-book2"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else					
							<div class="empty-list">
								<i class="icon-address-book2"></i>
								<span class="line-1">
									{{ trans('messages.list_empty_line_1') }}
								</span>
								<span class="line-2">
									{{ trans('messages.list_empty_line_2') }}
								</span>
							</div>
						@endif