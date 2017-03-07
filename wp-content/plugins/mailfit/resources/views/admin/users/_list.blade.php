                        @if ($users->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($users as $key => $item)
									<tr>
										<td width="1%">
											<img width="80" class="img-circle mr-10" src="{{ action('UserController@avatar', $item->uid) }}" alt="">
										</td>
										<td>
											<h5 class="no-margin text-bold">
												<a class="kq_search" href="{{ action('Admin\UserController@edit', $item->uid) }}">{{ $item->displayName() }}</a>
											</h5>
											<span class="text-muted kq_search">{{ $item->email }}</span>
											<br />
											{{ $item->userGroup->name }}
											<br />
											<span class="text-muted2">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
										</td>
										<td class="stat-fix-size">
											<div class="single-stat-box pull-left ml-20">
												<span class="no-margin text-teal-800 stat-num">{{ $item->displaySendingQuotaUsage() }}</span>
												<div class="progress progress-xxs">
													<div class="progress-bar progress-bar-info" style="width: {{ $item->getSendingQuotaUsagePercentage() }}%">
													</div>
												</div>
												<span class="text-muted"><strong>{{ $item->getSendingQuotaUsage() }}/{{ ($item->getSendingQuota() == -1) ? '∞' : $item->getSendingQuota() }}</strong>
													@if(($item->getSendingQuota() != -1))
														{{ trans('messages.display_sending_quota', [
															'number' => $item->userGroup->getOption('frontend', 'sending_quota_time'),
															'period' => trans('messages.' . $item->userGroup->getOption('frontend', 'sending_quota_time_unit'))
														]) }}
													@endif
												</span>
											</div>
											<!--<div class="single-stat-box pull-left ml-20">
												<span class="no-margin text-teal-800 stat-num">{{ $item->subscribersUsage() }}%</span>
												<div class="progress progress-xxs">
													<div class="progress-bar progress-bar-info" style="width: {{ $item->subscribersUsage() }}%">
													</div>
												</div>
												<span class="text-muted">{{ $item->subscribers()->count() }}/{{ $item->maxSubscribers() }} {{ trans('messages.subscribers') }}</span>
											</div>
											<div class="single-stat-box pull-left ml-20">
												<span class="no-margin text-teal-800 stat-num">{{ $item->listsUsage() }}%</span>
												<div class="progress progress-xxs">
													<div class="progress-bar progress-bar-info" style="width: {{ $item->listsUsage() }}%">
													</div>
												</div>
												<span class="text-muted"><strong>{{ $item->lists()->count() }}/{{ $item->maxLists() }}</strong> {{ trans('messages.lists') }}</span>
											</div>
											<div class="single-stat-box pull-left ml-20">
												<span class="no-margin text-teal-800 stat-num">{{ $item->campaignsUsage() }}%</span>
												<div class="progress progress-xxs">
													<div class="progress-bar progress-bar-info" style="width: {{ $item->campaignsUsage() }}%">
													</div>
												</div>
												<span class="text-muted"><strong>{{ $item->campaigns()->count() }}/{{ $item->maxCampaigns() }}</strong> {{ trans('messages.campaigns') }}</span>
											</div>-->
										</td>
										<td class="text-center">
											<div class="single-stat-box pull-left">
												<i class="table-checkmark-{{ $item->userGroup->backend_access }}"></i>
												<br />
												<span class="text-muted">{{ trans("messages.backend") }}</span>
											</div>
											<div class="single-stat-box pull-left">
												<i class="table-checkmark-{{ $item->userGroup->frontend_access }}"></i>
												<br />
												<span class="text-muted">{{ trans("messages.frontend") }}</span>
											</div>
										</td>
										<td class="text-right">
											<span class="text-muted2 list-status pull-left">
												<span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.user_status_' . $item->status) }}</span>
											</span>
											@can('switch_user', $item)
												<a href="{{ action('Admin\UserController@switch_user', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.login_as_this_user') }}" type="button" class="btn bg-teal-600 btn-icon"><i class="glyphicon glyphicon-random pr-5"></i></a>
											@endcan
											@can('update', $item)
												<a href="{{ action('Admin\UserController@edit', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey-600 btn-icon"><i class="icon icon-pencil pr-0 mr-0"></i></a>
											@endcan
											@can('delete', $item)
												<div class="btn-group">										
													<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
													<ul class="dropdown-menu dropdown-menu-right">													
														<li>														
															<a delete-confirm="{{ trans('messages.delete_users_confirm') }}" href="{{ action('Admin\UserController@delete', ['uids' => $item->uid]) }}">
																<i class="icon-trash"></i> {{ trans('messages.delete') }}
															</a>
														</li>
													</ul>
												</div>
											@endcan
										</td>
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select')
							{{ $users->links() }}
						@elseif (!empty(request()->keyword))
							<div class="empty-list">
								<i class="icon-users"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else					
							<div class="empty-list">
								<i class="icon-users"></i>
								<span class="line-1">
									{{ trans('messages.user_empty_line_1') }}
								</span>
							</div>
						@endif
