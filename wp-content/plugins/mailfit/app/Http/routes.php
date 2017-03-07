<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::resource('automation', 'AutomationController');
Route::group(['middleware' => ['installed']], function () {
    // Installation
    Route::get('install', 'InstallController@starting');
    Route::get('install/site-info', 'InstallController@siteInfo');
    Route::post('install/site-info', 'InstallController@siteInfo');
    Route::get('install/system-compatibility', 'InstallController@systemCompatibility');
    Route::get('install/database', 'InstallController@database');
    Route::post('install/database', 'InstallController@database');
    Route::get('install/database_import', 'InstallController@databaseImport');
    Route::get('install/cron-jobs', 'InstallController@cronJobs');
    Route::post('install/cron-jobs', 'InstallController@cronJobs');
    Route::get('install/finishing', 'InstallController@finishing');
    Route::get('install/finish', 'InstallController@finish');
});

Route::group(['namespace' => 'Api', 'prefix' => 'api/v1', 'middleware' => 'auth:api'], function () {
    // Route::group(['namespace' => 'Api', 'prefix' => 'api/v1'], function () {
    Route::get('', 'HomeController@index');
    
    // List
    Route::resource('lists', 'MailListController');
    
    // Campaign
    Route::resource('campaigns', 'CampaignController');
    
    // Subscriber
    Route::get('lists/{list_uid}/subscribers', 'SubscriberController@index');
    Route::get('lists/{list_uid}/subscribers/{uid}', 'SubscriberController@show');
    Route::patch('lists/{list_uid}/subscribers/{uid}/subscribe', 'SubscriberController@subscribe');
    Route::patch('lists/{list_uid}/subscribers/{uid}/unsubscribe', 'SubscriberController@unsubscribe');
    Route::delete('lists/{list_uid}/subscribers/{uid}/delete', 'SubscriberController@delete');
    Route::post('lists/{list_uid}/subscribers/store', 'SubscriberController@store');
    
    // User
    Route::resource('users', 'UserController');
    
    // Automation
    Route::post('automations/{uid}/api/call', 'AutomationController@apiCall');
});

Route::auth();

Route::get('/database/error', 'Controller@databaseConnectionError');
Route::get('/offline', 'Controller@offline');
Route::get('/not-authorized', 'Controller@notAuthorized');
Route::get('/demo', 'Controller@demo');
Route::get('/autologin/{api_token}', 'Controller@autoLogin');
Route::get('/reload/cache', 'Controller@reloadCache');
Route::get('/migrate/run', 'Controller@runMigration');

Route::group(['middleware' => ['not_installed', 'frontend']], function () {
    Route::get('/', 'HomeController@index');
    Route::get('/docs/api/v1', 'Controller@docsApiV1');
    
    Route::get('/current_user_uid', 'UserController@showUid');

    // Update current user profile
    Route::get('account/api/renew', 'AccountController@renewToken');
    Route::get('account/api', 'AccountController@api');
    Route::get('account/profile', 'AccountController@profile');
    Route::post('account/profile', 'AccountController@profile');
    Route::get('account/contact', 'AccountController@contact');
    Route::post('account/contact', 'AccountController@contact');
    Route::get('account/logs', 'AccountController@logs');
    Route::get('account/logs/listing', 'AccountController@logsListing');
    Route::get('account/quota_log', 'AccountController@quotaLog');

    // Translation data
    Route::get('/datatable_locale', 'Controller@datatable_locale');
    Route::get('/jquery_validate_locale', 'Controller@jquery_validate_locale');

    // User avatar
    Route::get('assets/images/avatar/user-{uid?}.jpg', 'UserController@avatar');

    // Mail list
    Route::post('lists/copy', 'MailListController@copy');
    Route::get('lists/quick-view', 'MailListController@quickView');
    Route::get('lists/{uid}/list-growth', 'MailListController@listGrowthChart');
    Route::get('lists/{uid}/list-statistics-chart', 'MailListController@statisticsChart');
    Route::get('lists/sort', 'MailListController@sort');
    Route::get('lists/listing/{page?}', 'MailListController@listing');
    Route::get('lists/delete', 'MailListController@delete');
    Route::get('lists/delete/confirm', 'MailListController@deleteConfirm');
    Route::get('lists/{uid}/overview', 'MailListController@overview');
    Route::resource('lists', 'MailListController');
    Route::get('lists/{uid}/edit', 'MailListController@edit');
    Route::patch('lists/{uid}/update', 'MailListController@update');
    Route::get('lists/{uid}/embedded-form', 'MailListController@embeddedForm');
    Route::post('lists/{uid}/embedded-form-subscribe', 'MailListController@embeddedFormSubscribe');
    Route::post('lists/{uid}/embedded-form-subscribe-captcha', 'MailListController@embeddedFormCaptcha');
    Route::get('lists/{uid}/embedded-form-frame', 'MailListController@embeddedFormFrame');

    // Field
    Route::get('lists/{list_uid}/fields', 'FieldController@index');
    Route::get('lists/{list_uid}/fields/sort', 'FieldController@sort');
    Route::post('lists/{list_uid}/fields/store', 'FieldController@store');
    Route::get('lists/{list_uid}/fields/sample/{type}', 'FieldController@sample');
    Route::get('lists/{list_uid}/fields/{uid}/delete', 'FieldController@delete');

    // Subscriber
    Route::get('lists/{list_uid}/subscribers/wordpress/import/list', 'MailListController@wordpressImportList');
    Route::post('lists/{list_uid}/subscribers/wordpress/import', 'MailListController@wordpressImport');
    Route::get('lists/{list_uid}/subscribers/wordpress/import', 'MailListController@wordpressImport');
    Route::get('lists/{list_uid}/subscribers/wordpress/import/proccess', 'MailListController@wordpressImportProccess');
    
    Route::get('subscribers/move/{list_uid}/{type}', 'SubscriberController@move');
    Route::get('subscribers/copy/{list_uid}/{type}', 'SubscriberController@copy');
    Route::get('lists/{list_uid}/subscribers', 'SubscriberController@index');
    Route::get('lists/{list_uid}/subscribers/create', 'SubscriberController@create');
    Route::get('lists/{list_uid}/subscribers/listing', 'SubscriberController@listing');
    Route::post('lists/{list_uid}/subscribers/store', 'SubscriberController@store');
    Route::get('lists/{list_uid}/subscribers/{uid}/edit', 'SubscriberController@edit');
    Route::patch('lists/{list_uid}/subscribers/{uid}/update', 'SubscriberController@update');
    Route::get('lists/{list_uid}/subscribers/delete', 'SubscriberController@delete');
    Route::get('lists/{list_uid}/subscribers/subscribe', 'SubscriberController@subscribe');
    Route::get('lists/{list_uid}/subscribers/unsubscribe', 'SubscriberController@unsubscribe');
    Route::get('lists/{list_uid}/subscribers/import', 'SubscriberController@import');
    Route::post('lists/{list_uid}/subscribers/import', 'SubscriberController@import');
    Route::get('lists/{list_uid}/subscribers/import/list', 'SubscriberController@importList');
    Route::get('lists/{list_uid}/subscribers/import/log', 'SubscriberController@downloadImportLog');
    Route::get('lists/{list_uid}/subscribers/import/proccess', 'SubscriberController@importProccess');
    Route::get('lists/{list_uid}/subscribers/export', 'SubscriberController@export');
    Route::post('lists/{list_uid}/subscribers/export', 'SubscriberController@export');
    Route::get('lists/{list_uid}/subscribers/export/proccess', 'SubscriberController@exportProccess');
    Route::get('lists/{list_uid}/subscribers/export/download', 'SubscriberController@downloadExportedCsv');
    Route::get('lists/{list_uid}/subscribers/export/list', 'SubscriberController@exportList');

    // Notification handler
    Route::post('delivery/notify/{stype}', 'DeliveryController@notify');
    Route::get('delivery/notify/{stype}', 'DeliveryController@notify');

    // Segment
    Route::get('segments/select_box', 'SegmentController@selectBox');
    Route::get('lists/{list_uid}/segments', 'SegmentController@index');
    Route::get('lists/{list_uid}/segments/{uid}/subscribers', 'SegmentController@subscribers');
    Route::get('lists/{list_uid}/segments/{uid}/listing_subscribers', 'SegmentController@listing_subscribers');
    Route::get('lists/{list_uid}/segments/create', 'SegmentController@create');
    Route::get('lists/{list_uid}/segments/listing', 'SegmentController@listing');
    Route::post('lists/{list_uid}/segments/store', 'SegmentController@store');
    Route::get('lists/{list_uid}/segments/{uid}/edit', 'SegmentController@edit');
    Route::patch('lists/{list_uid}/segments/{uid}/update', 'SegmentController@update');
    Route::get('lists/{list_uid}/segments/delete', 'SegmentController@delete');
    Route::get('lists/{list_uid}/segments/sample_condition', 'SegmentController@sample_condition');

    // Page
    Route::get('lists/{list_uid}/pages/{alias}/update', 'PageController@update');
    Route::post('lists/{list_uid}/pages/{alias}/update', 'PageController@update');
    Route::post('lists/{list_uid}/pages/{alias}/preview', 'PageController@preview');
    Route::get('lists/{list_uid}/sign-up', 'PageController@signUpForm');
    Route::post('lists/{list_uid}/sign-up', 'PageController@signUpForm');
    Route::get('lists/{list_uid}/sign-up/thank-you', 'PageController@signUpThankyouPage');
    Route::get('lists/{list_uid}/subscribe-confirm/{uid}/{code}', 'PageController@signUpConfirmationThankyou');
    Route::get('lists/{list_uid}/unsubscribe/{uid}/{code}', 'PageController@unsubscribeForm');
    Route::post('lists/{list_uid}/unsubscribe/{uid}/{code}', 'PageController@unsubscribeForm');
    Route::get('lists/{list_uid}/update-profile/{uid}/{code}', 'PageController@profileUpdateForm');
    Route::post('lists/{list_uid}/update-profile/{uid}/{code}', 'PageController@profileUpdateForm');
    Route::get('lists/{list_uid}/update-profile-success/{uid}', 'PageController@profileUpdateSuccessPage');
    Route::get('lists/{list_uid}/profile-update-email-sent/{uid}', 'PageController@profileUpdateEmailSent');
    Route::get('lists/{list_uid}/unsubscribe-success/{uid}', 'PageController@unsubscribeSuccessPage');

    // Template
    Route::get('templates/{uid}/content', 'TemplateController@content');
    Route::get('templates/sort', 'TemplateController@sort');
    Route::get('templates/listing/{page?}', 'TemplateController@listing');
    Route::get('templates/choosing/{campaign_uid}/{page?}', 'TemplateController@choosing');
    Route::get('templates/upload', 'TemplateController@upload');
    Route::post('templates/upload', 'TemplateController@upload');
    Route::get('templates/{uid}/image', 'TemplateController@image');
    Route::post('templates/{uid}/saveImage', 'TemplateController@saveImage');
    Route::get('templates/{uid}/preview', 'TemplateController@preview');
    Route::get('templates/delete', 'TemplateController@delete');
    Route::get('templates/build/select', 'TemplateController@buildSelect');
    Route::get('templates/build/{style?}', 'TemplateController@build');
    Route::get('templates/{uid}/rebuild', 'TemplateController@rebuild');
    Route::resource('templates', 'TemplateController');
    Route::get('templates/{uid}/edit', 'TemplateController@edit');
    Route::patch('templates/{uid}/update', 'TemplateController@update');

    // Campaign
    Route::get('campaigns/{uid}/list-segment-form', 'CampaignController@listSegmentForm');
    Route::get('campaigns/{uid}/template/review', 'CampaignController@templateReview');
    Route::post('campaigns/{uid}/image/save', 'CampaignController@saveImage');
    Route::get('campaigns/{uid}/image', 'CampaignController@image');
    Route::get('campaigns/{uid}/preview', 'CampaignController@preview');
    Route::get('campaigns/templates/list', 'CampaignController@templateList');
    Route::patch('campaigns/{uid}/templates/choose/from/{from_uid}', 'CampaignController@campaignTemplateChoose');
    Route::post('campaigns/send-test-email', 'CampaignController@sendTestEmail');
    Route::get('campaigns/delete/confirm', 'CampaignController@deleteConfirm');
    Route::get('campaigns/{message_id}/open', 'CampaignController@open');
    Route::get('campaigns/{message_id}/click/{url}', 'CampaignController@click');
    Route::get('campaigns/{message_id}/unsubscribe', 'CampaignController@unsubscribe');

    Route::post('campaigns/copy', 'CampaignController@copy');
    Route::get('campaigns/{uid}/subscribers', 'CampaignController@subscribers');
    Route::get('campaigns/{uid}/subscribers/listing', 'CampaignController@subscribersListing');
    Route::get('campaigns/{uid}/open-map', 'CampaignController@openMap');
    Route::get('campaigns/{uid}/tracking-log', 'CampaignController@trackingLog');
    Route::get('campaigns/{uid}/tracking-log/listing', 'CampaignController@trackingLogListing');
    Route::get('campaigns/{uid}/bounce-log', 'CampaignController@bounceLog');
    Route::get('campaigns/{uid}/bounce-log/listing', 'CampaignController@bounceLogListing');
    Route::get('campaigns/{uid}/feedback-log', 'CampaignController@feedbackLog');
    Route::get('campaigns/{uid}/feedback-log/listing', 'CampaignController@feedbackLogListing');
    Route::get('campaigns/{uid}/open-log', 'CampaignController@openLog');
    Route::get('campaigns/{uid}/open-log/listing', 'CampaignController@openLogListing');
    Route::get('campaigns/{uid}/click-log', 'CampaignController@clickLog');
    Route::get('campaigns/{uid}/click-log/listing', 'CampaignController@clickLogListing');
    Route::get('campaigns/{uid}/unsubscribe-log', 'CampaignController@unsubscribeLog');
    Route::get('campaigns/{uid}/unsubscribe-log/listing', 'CampaignController@unsubscribeLogListing');

    Route::get('campaigns/quick-view', 'CampaignController@quickView');
    Route::get('campaigns/{uid}/chart24h', 'CampaignController@chart24h');
    Route::get('campaigns/{uid}/chart', 'CampaignController@chart');
    Route::get('campaigns/{uid}/chart/countries/open', 'CampaignController@chartCountry');
    Route::get('campaigns/{uid}/chart/countries/click', 'CampaignController@chartClickCountry');
    Route::get('campaigns/{uid}/overview', 'CampaignController@overview');
    Route::get('campaigns/{uid}/links', 'CampaignController@links');
    Route::get('campaigns/sort', 'CampaignController@sort');
    Route::get('campaigns/listing/{page?}', 'CampaignController@listing');
    Route::get('campaigns/{uid}/recipients', 'CampaignController@recipients');
    Route::post('campaigns/{uid}/recipients', 'CampaignController@recipients');
    Route::get('campaigns/{uid}/setup', 'CampaignController@setup');
    Route::post('campaigns/{uid}/setup', 'CampaignController@setup');
    Route::get('campaigns/{uid}/template', 'CampaignController@template');
    Route::post('campaigns/{uid}/template', 'CampaignController@template');
    Route::get('campaigns/{uid}/template/select', 'CampaignController@templateSelect');
    Route::get('campaigns/{uid}/template/choose/{template_uid}', 'CampaignController@templateChoose');
    Route::get('campaigns/{uid}/template/preview', 'CampaignController@templatePreview');
    Route::get('campaigns/{uid}/template/iframe', 'CampaignController@templateIframe');
    Route::get('campaigns/{uid}/template/build/{style}', 'CampaignController@templateBuild');
    Route::get('campaigns/{uid}/template/rebuild', 'CampaignController@templateRebuild');
    Route::get('campaigns/{uid}/schedule', 'CampaignController@schedule');
    Route::post('campaigns/{uid}/schedule', 'CampaignController@schedule');
    Route::get('campaigns/{uid}/confirm', 'CampaignController@confirm');
    Route::post('campaigns/{uid}/confirm', 'CampaignController@confirm');
    Route::get('campaigns/delete', 'CampaignController@delete');
    Route::get('campaigns/select2', 'CampaignController@select2');
    Route::get('campaigns/pause', 'CampaignController@pause');
    Route::get('campaigns/restart', 'CampaignController@restart');
    Route::resource('campaigns', 'CampaignController');
    Route::get('campaigns/{uid}/edit', 'CampaignController@edit');
    Route::patch('campaigns/{uid}/update', 'CampaignController@update');    
    
    Route::get('users/login-back', 'UserController@loginBack');
    
    // System job
    Route::post('systems/jobs/cancel', 'SystemJobController@cancel');
    Route::get('systems/jobs/{type}/listing', 'SystemJobController@listing');
    Route::get('systems/jobs/delete', 'SystemJobController@delete');
    Route::get('systems/jobs/{id}/download/log', 'SystemJobController@downloadLog');
    Route::get('systems/jobs/{id}/download/csv', 'SystemJobController@downloadCsv');
    
    // Automation
    Route::get('automations/{uid}/list-segment-form', 'AutomationController@listSegmentForm');    
    Route::patch('automations/disable', 'AutomationController@disable');
    Route::patch('automations/enable', 'AutomationController@enable');
    Route::get('automations/{uid}/overview/emails/list', 'AutomationController@overviewCampaignsList');
    Route::get('automations/{uid}/overview/emails', 'AutomationController@overviewCampaigns');
    Route::get('automations/{uid}/overview/workflow', 'AutomationController@overviewWorkflow');
    Route::post('automations/{uid}/confirm', 'AutomationController@confirm');
    Route::get('automations/{uid}/confirm', 'AutomationController@confirm');
    Route::delete('automations/delete', 'AutomationController@delete');
    Route::get('automations/{uid}/auto-event/form', 'AutomationController@nextEventForm');
    Route::post('automations/{uid}/workflow', 'AutomationController@workflow');
    Route::get('automations/{uid}/workflow', 'AutomationController@workflow');
    Route::get('automations/{uid}/custom-criteria/form', 'AutomationController@criteriaForm');
    Route::post('automations/{uid}/trigger', 'AutomationController@trigger');
    Route::get('automations/{uid}/trigger', 'AutomationController@trigger');
    Route::get('automations/sort', 'AutomationController@sort');
    Route::get('automations/listing/{page?}', 'AutomationController@listing');
    Route::resource('automations', 'AutomationController');
    
    // Auto event
    Route::get('auto-events/{uid}/emails/{campaign_uid}/template', 'AutoEventController@template'); //
    Route::post('auto-events/{uid}/emails/{campaign_uid}/template', 'AutoEventController@template'); //
    Route::get('auto-events/{uid}/emails/{campaign_uid}/template/select', 'AutoEventController@templateSelect'); //
    Route::get('auto-events/{uid}/emails/{campaign_uid}/template/choose/{template_uid}', 'AutoEventController@templateChoose');
    Route::get('auto-events/{uid}/emails/{campaign_uid}/template/preview', 'AutoEventController@templatePreview'); //
    Route::get('auto-events/{uid}/emails/{campaign_uid}/template/iframe', 'AutoEventController@templateIframe'); //
    Route::get('auto-events/{uid}/emails/{campaign_uid}/template/build/{style}', 'AutoEventController@templateBuild'); //
    Route::get('auto-events/{uid}/emails/{campaign_uid}/template/rebuild', 'AutoEventController@templateRebuild'); //
    
    Route::patch('auto-events/{uid}/move/up', 'AutoEventController@moveUp');
    Route::patch('auto-events/{uid}/move/down', 'AutoEventController@moveDown');
    Route::patch('auto-events/{uid}/disable', 'AutoEventController@disable');
    Route::patch('auto-events/{uid}/enable', 'AutoEventController@enable');
    Route::patch('auto-events/{uid}/emails/{campaign_uid}/setup', 'AutoEventController@campaignSetup');
    Route::get('auto-events/{uid}/emails/{campaign_uid}/setup', 'AutoEventController@campaignSetup');
    Route::delete('auto-events/{uid}/delete', 'AutoEventController@delete');
    Route::delete('auto-events/campaigns/{uid}/delete', 'AutoEventController@deleteCampaign');
    Route::post('auto-events/{uid}/campaigns/add', 'AutoEventController@addCampaign');
    Route::get('auto-events/{uid}/campaigns', 'AutoEventController@campaigns');
    Route::resource('auto-events', 'AutoEventController');
});

// ADMIN AREA
Route::group(['namespace' => 'Admin', 'middleware' => ['not_installed', 'backend']], function () {
    Route::get('admin', 'HomeController@index');

    // User Group
    Route::get('admin/user_groups/listing/{page?}', 'UserGroupController@listing');
    Route::get('admin/user_groups/sort', 'UserGroupController@sort');
    Route::get('admin/user_groups/delete', 'UserGroupController@delete');
    Route::resource('admin/user_groups', 'UserGroupController');

    // User    
    Route::get('admin/users/switch/{uid}', 'UserController@switch_user');
    Route::get('admin/users/listing/{page?}', 'UserController@listing');
    Route::get('admin/users/sort', 'UserController@sort');
    Route::get('admin/users/delete', 'UserController@delete');
    Route::resource('admin/users', 'UserController');

    // Template
    Route::get('admin/templates/listing/{page?}', 'TemplateController@listing');
    Route::get('admin/templates/upload', 'TemplateController@upload');
    Route::post('admin/templates/upload', 'TemplateController@upload');
    Route::get('admin/templates/delete', 'TemplateController@delete');
    Route::get('admin/templates/build/select', 'TemplateController@buildSelect');
    Route::get('admin/templates/build/{style?}', 'TemplateController@build');
    Route::get('admin/templates/{uid}/rebuild', 'TemplateController@rebuild');
    Route::resource('admin/templates', 'TemplateController');
    Route::get('admin/templates/{uid}/edit', 'TemplateController@edit');
    Route::patch('admin/templates/{uid}/update', 'TemplateController@update');

    // Layout
    Route::get('admin/layouts/listing/{page?}', 'LayoutController@listing');
    Route::get('admin/layouts/sort', 'LayoutController@sort');
    Route::resource('admin/layouts', 'LayoutController');

    // Sending servers
    Route::get('admin/sending_servers/select', 'SendingServerController@select');
    Route::get('admin/sending_servers/listing/{page?}', 'SendingServerController@listing');
    Route::get('admin/sending_servers/sort', 'SendingServerController@sort');
    Route::get('admin/sending_servers/delete', 'SendingServerController@delete');
    Route::get('admin/sending_servers/disable', 'SendingServerController@disable');
    Route::get('admin/sending_servers/enable', 'SendingServerController@enable');
    Route::resource('admin/sending_servers', 'SendingServerController');
    Route::get('admin/sending_servers/create/{type}', 'SendingServerController@create');
    Route::post('admin/sending_servers/create/{type}', 'SendingServerController@store');
    Route::get('admin/sending_servers/{id}/edit/{type}', 'SendingServerController@edit');
    Route::patch('admin/sending_servers/{id}/update/{type}', 'SendingServerController@update');
    

    // Bounce handlers
    Route::get('admin/bounce_handlers/listing/{page?}', 'BounceHandlerController@listing');
    Route::get('admin/bounce_handlers/sort', 'BounceHandlerController@sort');
    Route::get('admin/bounce_handlers/delete', 'BounceHandlerController@delete');
    Route::resource('admin/bounce_handlers', 'BounceHandlerController');

    // Feedback Loop handlers
    Route::get('admin/feedback_loop_handlers/listing/{page?}', 'FeedbackLoopHandlerController@listing');
    Route::get('admin/feedback_loop_handlers/sort', 'FeedbackLoopHandlerController@sort');
    Route::get('admin/feedback_loop_handlers/delete', 'FeedbackLoopHandlerController@delete');
    Route::resource('admin/feedback_loop_handlers', 'FeedbackLoopHandlerController');

    // Sending domain
    Route::get('admin/sending_domains/listing/{page?}', 'SendingDomainController@listing');
    Route::get('admin/sending_domains/sort', 'SendingDomainController@sort');
    Route::get('admin/sending_domains/delete', 'SendingDomainController@delete');
    Route::resource('admin/sending_domains', 'SendingDomainController');

    // Language
    Route::get('admin/languages/delete/confirm', 'LanguageController@deleteConfirm');
    Route::get('admin/languages/listing/{page?}', 'LanguageController@listing');
    Route::get('admin/languages/delete', 'LanguageController@delete');
    Route::get('admin/languages/{id}/translate/{file}', 'LanguageController@translate');
    Route::post('admin/languages/{id}/translate/{file}', 'LanguageController@translate');
    Route::get('admin/languages/disable', 'LanguageController@disable');
    Route::get('admin/languages/enable', 'LanguageController@enable');
    Route::get('admin/languages/{id}/download', 'LanguageController@download');
    Route::get('admin/languages/{id}/upload', 'LanguageController@upload');
    Route::post('admin/languages/{id}/upload', 'LanguageController@upload');
    Route::resource('admin/languages', 'LanguageController');

    // Setting
    Route::get('admin/settings/mailer', 'SettingController@mailer');
    Route::post('admin/settings/mailer', 'SettingController@mailer');
    Route::get('admin/settings/cronjob', 'SettingController@cronjob');
    Route::post('admin/settings/cronjob', 'SettingController@cronjob');
    Route::get('admin/settings/urls', 'SettingController@urls');
    Route::get('admin/settings/sending', 'SettingController@sending');
    Route::post('admin/settings/sending', 'SettingController@sending');
    Route::get('admin/settings/general', 'SettingController@general');
    Route::post('admin/settings/general', 'SettingController@general');
    Route::get('admin/settings/logs', 'SettingController@logs');
    Route::get('log', 'SettingController@download_log');
    Route::get('admin/settings/{tab?}', 'SettingController@index');
    Route::post('admin/settings', 'SettingController@index');
    Route::get('admin/update-urls', 'SettingController@updateUrls');
    

    // Tracking log
    Route::get('admin/tracking_log', 'TrackingLogController@index');
    Route::get('admin/tracking_log/listing', 'TrackingLogController@listing');

    // Feedback log
    Route::get('admin/bounce_log', 'BounceLogController@index');
    Route::get('admin/bounce_log/listing', 'BounceLogController@listing');

    // Open log
    Route::get('admin/open_log', 'OpenLogController@index');
    Route::get('admin/open_log/listing', 'OpenLogController@listing');

    // Click log
    Route::get('admin/click_log', 'ClickLogController@index');
    Route::get('admin/click_log/listing', 'ClickLogController@listing');

    // Feedback log
    Route::get('admin/feedback_log', 'FeedbackLogController@index');
    Route::get('admin/feedback_log/listing', 'FeedbackLogController@listing');

    // Unsubscribe log
    Route::get('admin/unsubscribe_log', 'UnsubscribeLogController@index');
    Route::get('admin/unsubscribe_log/listing', 'UnsubscribeLogController@listing');

    // Sending domain
    Route::get('admin/blacklist', 'BlacklistController@index');
    Route::get('admin/blacklist/listing', 'BlacklistController@listing');
    Route::get('admin/blacklist/delete', 'BlacklistController@delete');
});
