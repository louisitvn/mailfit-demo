8.1.1 / 2017-02-06
=====================

 * Added: support advanced background job installation wizard
 * Added: support erasing existing database before initialization
 * Added: support customizing system's default language
 * Fixed: issue with auto login by token

8.0.2 / 2017-01-30
=====================

 * Added: support updating the application configuration without re-installing
 * Added: support sending campaign without using cronjob
 * Added: support sending campaign to multiple lists / segments
 * Changed: new quota renewal method
 * Fixed: timezone issue while scheduling future campaign
 * Fixed: issue of migrating from v2.0.4
 * Fixed: links sometimes do not work in test email
 * Fixed: intermittent memory issue of PHP 5.6 or below
 * Fixed: sending server's quota 
 * Fixed: file manager URL issue with old browsers

7.4.0 / 2017-01-17
===================

 * Fixed: memory limit issue with importing
 * Fixed: MAC OS line-ending issue
 * Fixed: follow-up email is triggered more than one time
 * Fixed: compatibility issues with old PHP versions
 * Fixed: compatibility issues with 
 * Fixed: calendar glitches on some browsers

7.3.3 / 2017-01-08
===================

 * Fixed: mail list export compatibility issue on certain systems
 * Fixed: cannot delete out-dated campaigns
 * Fixed: php-curl compatibility issue
 * Fixed: improve subscribers import performance
 * Fixed: PHP 7.1 compatibility issues
 * Added: support automation/autorespond functionality
 * Added: send a test email of campaign
 * Added: better internationalization support: allow creating new language
 * Added: better internationalization support: support custom translation
 * Added: send a test email of campaign
 * Added: better internationalization support: allow creating new language
 * Added: better internationalization support: support custom translation
 * Changed: support running several campaigns at the same time
 * Changed: support running several campaigns at the same time
 * Changed: php-xml is now required
 * Changed: refractor of the system jobs
 * Changed: only one cronjob is required

7.2.0 / 2016-11-01
==================

 * Fixed: certain encoding may cause corrupt links
 * Changed: default user policy change

7.1.0 / 2016-10-28
==================
 
 * Fixed: subscriber import does not work well with async
 * Fixed: runtime-message-id with extra invisible space
 * Fixed: directory permission checking error
 * Fixed: campaign's wrong subscribers count in certain cases
 * Fixed: config cache with invalid values

7.0.0 / 2016-10-23
==================

 * Added: ElasticEmail API/SMTP support
 * Added: create-user API
 * Added: quick login support
 * Added: copy campaign
 * Added: drag & drop email builder
 * Changed: delivery server encryption method is no longer required
 * Fixed: detect more environment dependencies when installing
 * Fixed: layout crashes for old IE browser
 * Fixed: application crashes when mbstring is missing
 * Fixed: chart view issues on MS Edge
 * Fixed: installation wizard compatibility issue
 * Fixed: certain types of links are not tracked
 * Fixed: reduce the delay time when sending email through SMTP

6.3.0 / 2016-10-02
==================

 * Fixed: open tracking causes broken image in email content
 * Fixed intermittent issues with bar chart in Safari
 * Changed click-to-open ratio is now based on open count

6.2.0 / 2016-09-30
==================

 * Fixed listing sometimes crashes due to slow internet connection
 * Fixed do not allow users to enter invalid IMAP encryption method
 * Fixed list import intermittent issue for ISO encoded CSV
 * Added pie chart visualization for top countries by open
 * Added pie chart visualization for top countries by click
 * Updated text & hints on the UI
 * Changed dashboard UI now contains more information
 * Changed click-rate is no longer computed based on specific URL

6.1.0 / 2016-09-27
==================

 * Fixed SSL issue for bounce handler
 * Fixed bounce handler does not work correctly for certain type of IMAP servers
 * Changed sending campaign can be deleted
 * Added full support for SendGrid (web API & SMTP)

6.0.1 / 2016-09-20
==================

 * Fixed HTML editor sometimes crashes on MS Edge 
 * Added clean up invalid bytes sequence in email content
 * Added check php-gd library availability in the installation wizard

6.0.0 / 2016-09-13
==================

This is the first publicly released version of Acelle Mail webapp (which was previously Turbo Mail 1.x, a private project at National Information System institute)

 * Fixed better compatibility with MS Edge browser
 * Multi-process support for sending large amounts of email
 * Added Mailgun API/SMTP integration full support
 * Added embeded form customization support
 * Added email extra headers for better RFC compliance
 * Added template gallery & template customization support
 * Added DKIM singing support for out-going message
 * Added better integration with Amazon SES
 * Added template preview support
 * Added bounce logging with more information
 * Changed refractor of quota system
