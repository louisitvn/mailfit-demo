<h3 class="mt-0 mb-10"><i class="icon-stats-bars4"></i> {{ trans("messages.used_quota") }}</h3>
<div class="row quota_box">
    <div class="col-md-12">
        <div class="content-group-sm mt-20">
            <div class="pull-right text-teal-800 text-semibold">
                <span class="text-muted">{{ Auth::user()->getSendingQuotaUsage() }}/{{ (Auth::user()->getSendingQuota() == -1) ? '∞' : Auth::user()->getSendingQuota() }}</span>
                &nbsp;&nbsp;&nbsp;{{ Auth::user()->displaySendingQuotaUsage() }}
            </div>
            <h5 class="text-semibold mb-5">{{ trans('messages.sending_quota') }}</h5>
            <div class="progress progress-xxs">
                <div class="progress-bar bg-warning" style="width: {{ Auth::user()->getSendingQuotaUsagePercentage() }}%">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="content-group-sm">
            <div class="pull-right text-teal-800 text-semibold">
                <span class="text-muted">{{ Auth::user()->listsCount() }}/{{ Auth::user()->maxLists() }}</span>
                &nbsp;&nbsp;&nbsp;{{ Auth::user()->displayListsUsage() }}
            </div>
            <h5 class="text-semibold mb-5">{{ trans('messages.list') }}</h5>
            <div class="progress progress-xxs">
                <div class="progress-bar bg-warning" style="width: {{ Auth::user()->listsUsage() }}%">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="content-group-sm mt-20">
            <div class="pull-right text-teal-800 text-semibold">
                <span class="text-muted progress-xxs">{{ Auth::user()->campaignsCount() }}/{{ Auth::user()->maxCampaigns() }}</span>
                &nbsp;&nbsp;&nbsp;{{ Auth::user()->displayCampaignsUsage() }}
            </div>
            <h5 class="text-semibold mb-5 mt-0">{{ trans('messages.campaign') }}</h5>
            <div class="progress progress-xxs">
                <div class="progress-bar bg-warning" style="width: {{ Auth::user()->campaignsUsage() }}%">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="content-group-sm">
            <div class="pull-right text-teal-800 text-semibold">
                <span class="text-muted">{{ Auth::user()->subscribersCount() }}/{{ Auth::user()->maxSubscribers() }}</span>
                &nbsp;&nbsp;&nbsp;{{ Auth::user()->displaySubscribersUsage() }}
            </div>
            <h5 class="text-semibold mb-5">{{ trans('messages.subscriber') }}</h5>
            <div class="progress progress-xxs">
                <div class="progress-bar bg-warning" style="width: {{ Auth::user()->subscribersUsage() }}%">
                </div>
            </div>
        </div>
    </div>
</div>
