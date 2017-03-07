<?php
/*
Plugin Name: MailFit
Plugin URI: http://wp.mailfitmail.com:3082/wp-admin
Description: MailFit Lite Email Marketing plugin for WordPress
Author: Luan Pham & Louis Pham
Version: 2.2.0
*/

// main MailFit app path
$dir_path = plugin_dir_path( __FILE__ );
$parts = explode('/', $dir_path);
$plugin_name = $parts[count($parts) - 2];
define("APP_PATH", plugins_url() . "/" .$plugin_name. "/public/");
define("DIR_PATH", $dir_path);

add_action( 'admin_menu', 'mailfit_admin_menu' );

// define mailfit main menu action
function mailfit_admin_menu() {
    // add top MailFit menu
	add_menu_page( 'MailFit',
        'MailFit',
        'manage_options',
        'mailfit.php',
        'mailfit_dashboard_menu',
        APP_PATH . 'images/wordpress-20x20.png',
        25
    );
    
    // add dashboard menu
    add_submenu_page( 'mailfit.php',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'mailfit.php',
        'mailfit_dashboard_menu'
    );
    
    // add campaigns menu
    add_submenu_page( 'mailfit.php',
        'Campaigns',
        'Campaigns',
        'manage_options',
        'mailfit_campaigns.php',
        'mailfit_campaigns_menu'
    );
	
	// add automations menu
    add_submenu_page( 'mailfit.php',
        'Automations',
        'Automations',
        'manage_options',
        'mailfit_automations.php',
        'mailfit_automations_menu'
    );
    
    // add lists menu
    add_submenu_page( 'mailfit.php',
        'Lists',
        'Lists',
        'manage_options',
        'mailfit_lists.php',
        'mailfit_lists_menu'
    );
	
	// add templates menu
    add_submenu_page( 'mailfit.php',
        'Templates',
        'Templates',
        'manage_options',
        'mailfit_templates.php',
        'mailfit_templates_menu'
    );
	
	// add sending servers menu
    add_submenu_page( 'mailfit.php',
        'Sending servers',
        'Sending servers',
        'manage_options',
        'mailfit_sending_servers.php',
        'mailfit_sending_servers_menu'
    );
	
	// add bounce handlers menu
    add_submenu_page( 'mailfit.php',
        'Bounce handlers',
        'Bounce handlers',
        'manage_options',
        'mailfit_bounce_handlers.php',
        'mailfit_bounce_handlers_menu'
    );
	
	// add sending domains menu
    add_submenu_page( 'mailfit.php',
        'Sending domains',
        'Sending domains',
        'manage_options',
        'mailfit_sending_domains.php',
        'mailfit_sending_domains_menu'
    );
	
	// add settings menu
    add_submenu_page( 'mailfit.php',
        'Settings',
        'Settings',
        'manage_options',
        'mailfit_settings.php',
        'mailfit_settings_menu'
    );
	
	// add layouts menu
    add_submenu_page( 'mailfit.php',
        'Layouts',
        'Layouts',
        'manage_options',
        'mailfit_layouts.php',
        'mailfit_layouts_menu'
    );
	
	// add logs menu
    add_submenu_page( 'mailfit.php',
        'Logs',
        'Logs',
        'manage_options',
        'mailfit_logs.php',
        'mailfit_logs_menu'
    );
	
	// add api menu
    add_submenu_page( 'mailfit.php',
        'API',
        'API',
        'manage_options',
        'mailfit_api.php',
        'mailfit_api_menu'
    );
}

// redirect to the spevific url
function mailfit_redirect($url){
    if (headers_sent()){
      die('<script type="text/javascript">window.location.href="' . $url . '";</script>');
    }else{
      header('Location: ' . $url);
      die();
    }
}

// layouts for wp plugin page
function mailfit_page($url) {
	wp_register_style('mailfit-wp', plugins_url('public/css/mailfit-wp.css',__FILE__ ));
	wp_enqueue_style('mailfit-wp');
	wp_register_script( 'mailfit-wp', plugins_url('public/js/mailfit-wp.js',__FILE__ ));
	wp_enqueue_script('mailfit-wp');

?>

<div class="mailfit-wp-container">
	<iframe scrolling="no" id="mailfit-wp-page-frame" class="page-frame" src="<?php echo $url; ?>"></iframe>
</div>

<?php
}

// MailFit dashboard menu click
function mailfit_dashboard_menu(){
	mailfit_page(APP_PATH);
}

// MailFit campaigns menu click
function mailfit_campaigns_menu(){
	mailfit_page(APP_PATH . "campaigns");
}

// MailFit automations menu click
function mailfit_automations_menu(){
	mailfit_page(APP_PATH . "automations"); /* Redirect browser */
}

// MailFit lists menu click
function mailfit_lists_menu(){
	mailfit_page(APP_PATH . "lists");
}

// MailFit templates menu click
function mailfit_templates_menu(){
	mailfit_page(APP_PATH . "admin/templates");
}

// MailFit sending servers menu click
function mailfit_sending_servers_menu(){
	mailfit_page(APP_PATH . "admin/sending_servers"); /* Redirect browser */
}

// MailFit bounce handlers click
function mailfit_bounce_handlers_menu(){
	mailfit_page(APP_PATH . "admin/bounce_handlers"); /* Redirect browser */
}

// MailFit sending domains click
function mailfit_sending_domains_menu(){
	mailfit_page(APP_PATH . "admin/sending_domains"); /* Redirect browser */
}

// MailFit settings click
function mailfit_settings_menu(){
	mailfit_page(APP_PATH . "admin/settings/sending"); /* Redirect browser */
}

// MailFit layouts click
function mailfit_layouts_menu(){
	mailfit_page(APP_PATH . "admin/layouts"); /* Redirect browser */
}

// MailFit logs click
function mailfit_logs_menu(){
	mailfit_page(APP_PATH . "admin/tracking_log"); /* Redirect browser */
}

// MailFit logs click
function mailfit_api_menu(){
	mailfit_page(APP_PATH . "account/api"); /* Redirect browser */
}

// Activate hook
function mailfit_activate()
{
	// Update folder/file permissions
	$oldmask = umask(0);	
	@chmod(DIR_PATH, 0755);
	@chmod(DIR_PATH . 'public', 0755);
	@chmod(DIR_PATH . 'public/index.php', 0644);
	umask($oldmask);
}
register_activation_hook( __FILE__, 'mailfit_activate' );