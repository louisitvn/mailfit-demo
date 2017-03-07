<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs nav-tabs-top page-second-nav">            
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
            @if (Auth::user()->getOption("backend", "report_blacklist") != 'no')
                <li rel0="BlacklistController">
                    <a href="{{ action('Admin\BlacklistController@index') }}">
                        <i class="glyphicon glyphicon-minus-sign"></i> {{ trans('messages.blacklist') }}
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>