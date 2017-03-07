<ul class="nav navbar-nav">
    <li rel0="HomeController">
        <a href="{{ action('HomeController@index') }}">
            <i class="icon-home"></i> {{ trans('messages.dashboard') }}
        </a>
    </li>
    <li rel0="CampaignController">
        <a href="{{ action('CampaignController@index') }}">
            <i class="icon-paperplane"></i> {{ trans('messages.campaigns') }}
        </a>
    </li>
	<li rel0="AutomationController">
		<a href="{{ action('AutomationController@index') }}">
			<i class="icon-alarm-check"></i> {{ trans('messages.Automations') }}
		</a>
	</li>
    <li
        rel0="MailListController"
        rel1="FieldController"
        rel2="SubscriberController"
        rel2="SegmentController"
    >
        <a href="{{ action('MailListController@index') }}"><i class="icon-address-book2"></i> {{ trans('messages.lists') }}</a>
    </li>	
    @if (Auth::user()->getOption("backend", "template_read") != 'no')
        <li rel0="TemplateController">
            <a href="{{ action('Admin\TemplateController@index') }}">
                <i class="icon-magazine"></i> {{ trans('messages.template') }}
            </a>
        </li>
    @endif
    @if (
        Auth::user()->getOption("backend", "sending_domain_read") != 'no'
        || Auth::user()->getOption("backend", "sending_server_read") != 'no'
        || Auth::user()->getOption("backend", "bounce_handler_read") != 'no'
        || Auth::user()->getOption("backend", "fbl_handler_read") != 'no'
    )
        <li class="dropdown language-switch"
            rel0="BounceHandlerController"
            rel1="FeedbackLoopHandlerController"
            rel2="SendingServerController"
            rel3="SendingDomainController"
        >
            <a class="dropdown-toggle" data-toggle="dropdown">
                <i class="glyphicon glyphicon-transfer"></i> {{ trans('messages.sending') }}
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                @if (Auth::user()->getOption("backend", "sending_server_read") != 'no')
                    <li rel0="SendingServerController">
                        <a href="{{ action('Admin\SendingServerController@index') }}">
                            <i class="icon-server"></i> {{ trans('messages.sending_severs') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "bounce_handler_read") != 'no')
                    <li rel0="BounceHandlerController">
                        <a href="{{ action('Admin\BounceHandlerController@index') }}">
                            <i class="glyphicon glyphicon-share"></i> {{ trans('messages.bounce_handlers') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "fbl_handler_read") != 'no')
                    <li rel0="FeedbackLoopHandlerController">
                        <a href="{{ action('Admin\FeedbackLoopHandlerController@index') }}">
                            <i class="glyphicon glyphicon-retweet"></i> {{ trans('messages.feedback_loop_handlers') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "sending_domain_read") != 'no')
                    <li rel0="SendingDomainController">
                        <a href="{{ action('Admin\SendingDomainController@index') }}">
                            <i class="icon-earth"></i> {{ trans('messages.sending_domains') }}
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <li class="dropdown language-switch"
        rel1="LayoutController"
        rel2="LanguageController"
        rel3="SettingController"
    >
        <a class="dropdown-toggle" data-toggle="dropdown">
            <i class="icon-gear"></i> {{ trans('messages.setting') }}
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
			<!--<li rel0="SettingController">
                <a href="{{ action('Admin\SettingController@sending') }}">
                    <i class="icon-equalizer2"></i> {{ trans('messages.all_settings') }}
                </a>
            </li>-->
            <li rel0="SettingController/sending">
                <a href="{{ action('Admin\SettingController@sending') }}">
					<i class="icon-paperplane"></i> {{ trans('messages.sending') }}</a>
            </li>
            <li rel0="SettingController/mailer">
                <a href="{{ action('Admin\SettingController@mailer') }}">
					<i class="icon-envelop"></i> {{ trans('messages.system_email') }}</a>
            </li>
            <li rel0="SettingController/cronjob">
                <a href="{{ action('Admin\SettingController@cronjob') }}">
					<i class="icon-alarm"></i> {{ trans('messages.background_job') }}</a>
            </li>
            @if (Auth::user()->getOption("backend", "layout_read") != 'no')
                <li rel0="LayoutController">
                    <a href="{{ action('Admin\LayoutController@index') }}">
                        <i class="glyphicon glyphicon-file"></i> {{ trans('messages.page_form_layout') }}
                    </a>
                </li>
            @endif
            @if (Auth::user()->getOption("backend", "language_read") != 'no')
                <li rel0="LanguageController">
                    <a href="{{ action('Admin\LanguageController@index') }}">
                        <i class="glyphicon glyphicon-flag"></i> {{ trans('messages.language') }}
                    </a>
                </li>
            @endif
            <li rel0="AccountController/api">
                <a href="{{ action("AccountController@api") }}" class="level-1">
                    <i class="icon-key position-left"></i> {{ trans('messages.api') }}
                </a>
            </li>
        </ul>
    </li>
    
    @if (
        Auth::user()->getOption("backend", "report_blacklist") != 'no'
        || Auth::user()->getOption("backend", "report_tracking_log") != 'no'
        || Auth::user()->getOption("backend", "report_bounce_log") != 'no'
        || Auth::user()->getOption("backend", "report_feedback_log") != 'no'
        || Auth::user()->getOption("backend", "report_open_log") != 'no'
        || Auth::user()->getOption("backend", "report_click_log") != 'no'
        || Auth::user()->getOption("backend", "report_unsubscribe_log") != 'no'
    )
        <li class="dropdown language-switch"
             rel0="TrackingLogController"
             rel1="OpenLogController"
             rel2="ClickLogController"
             rel3="FeedbackLogController"
             rel4="BlacklistController"
             rel5="UnsubscribeLogController"
             rel6="BounceLogController"
        >
            <a class="dropdown-toggle" data-toggle="dropdown">
                <i class="icon-file-text2"></i> {{ trans('messages.report') }}
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                @if (Auth::user()->getOption("backend", "report_blacklist") != 'no')
                    <li rel0="BlacklistController">
                        <a href="{{ action('Admin\BlacklistController@index') }}">
                            <i class="glyphicon glyphicon-minus-sign"></i> {{ trans('messages.blacklist') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "report_tracking_log") != 'no')
                    <li rel0="TrackingLogController">
                        <a href="{{ action('Admin\TrackingLogController@index') }}">
                            <i class="icon-file-text2"></i> {{ trans('messages.tracking_log') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "report_bounce_log") != 'no')
                    <li rel0="BounceLogController">
                        <a href="{{ action('Admin\BounceLogController@index') }}">
                            <i class="icon-file-text2"></i> {{ trans('messages.bounce_log') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "report_feedback_log") != 'no')
                    <li rel0="FeedbackLogController">
                        <a href="{{ action('Admin\FeedbackLogController@index') }}">
                            <i class="icon-file-text2"></i> {{ trans('messages.feedback_log') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "report_open_log") != 'no')
                    <li rel0="OpenLogController">
                        <a href="{{ action('Admin\OpenLogController@index') }}">
                            <i class="icon-file-text2"></i> {{ trans('messages.open_log') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "report_click_log") != 'no')
                    <li rel0="ClickLogController">
                        <a href="{{ action('Admin\ClickLogController@index') }}">
                            <i class="icon-file-text2"></i> {{ trans('messages.click_log') }}
                        </a>
                    </li>
                @endif
                @if (Auth::user()->getOption("backend", "report_unsubscribe_log") != 'no')
                    <li rel0="UnsubscribeLogController">
                        <a href="{{ action('Admin\UnsubscribeLogController@index') }}">
                            <i class="icon-file-text2"></i> {{ trans('messages.unsubscribe_log') }}
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
</ul>