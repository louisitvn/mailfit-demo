<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

class InstallController extends Controller
{
    // check for current step
    public function step($request)
    {
        $step = 0;

        $data = $request->session()->get('compatibilities');
        if (isset($data)) {
            $step = 1;
        } else {
            return $step;
        }
        
        //// Ignore step 2 3 if WordPress plugin
        $step = 4;

        $data = $request->session()->get('database_imported');
        if (isset($data)) {
            $step = 5;
        } else {
            return $step;
        }

        $data = $request->session()->get('cron_jobs');
        if (isset($data)) {
            $step = 6;
        } else {
            return $step;
        }

        return $step;
    }

    // Starting installation
    public function starting(Request $request)
    {        
        $next = action('InstallController@systemCompatibility');
        artisan_config_cache();
        return redirect()->away($next);
    }

    public function systemCompatibility(Request $request)
    {
        // Begin check
        $request->session()->forget('compatibilities');

        $compatibilities = $this->checkSystemCompatibility();
        $result = true;
        foreach ($compatibilities as $compatibility) {
            if (!$compatibility['check']) {
                $result = false;
            }
        }
        
        // retry if something not work yet
        try {
            if ($result) {
                $request->session()->set('compatibilities', $compatibilities);
            }        
        
            return view('install.compatibilities', [
                'compatibilities' => $compatibilities,
                'result' => $result,
                'step' => $this->step($request),
                'current' => 1,
            ]);
        } catch (\Exception $e) {
            $next_page = action('InstallController@systemCompatibility');
            artisan_config_cache();
            return redirect()->away($next_page);
        }
    }

    public function siteInfo(Request $request)
    {
        if ($this->step($request) < 1) {
            return redirect()->action('InstallController@systemCompatibility');
        }
        
        $site_info = 'empty';
        
        $request->session()->set('site_info', $site_info);
        return redirect()->action('InstallController@database');

    }

    // Database configuration
    public function database(Request $request)
    {
        if ($this->step($request) < 2) {
            return redirect()->action('InstallController@siteInfo');
        }

        $database = 'empty';        
        $request->session()->set('database', $database);
        
        $next_page = action('InstallController@databaseImport');
        artisan_config_cache();
        return redirect()->away($next_page);
    }

    // Import Database
    public function databaseImport(Request $request)
    {
        global $wpdb;
        $prefix = $wpdb->prefix . 'mailfit_';
        
        if ($this->step($request) < 3) {
            return redirect()->action('InstallController@database');
        }

        $database = $request->session()->get('database');
        $site_info = $request->session()->get('site_info');
        
        // Check if database is not empty
        $rules = [];
        $tables_exist = false;
        $prefix_check = empty($prefix) ? '' : "  AND table_name LIKE '".$prefix."%'";
        $result = $wpdb->get_results("SELECT COUNT(DISTINCT `table_name`) as count FROM `information_schema`.`columns` WHERE `table_schema` = '".$wpdb->dbname."'".$prefix_check);
        $result = $result[0];
        if ($result->count > 0) {
            $tables_exist = true;
        }
        
        if ($request->action == 'import') {
            $request->session()->forget('database_imported');

            // Drop all old table
            try {
                $delete_all_tables_query = "SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' )
                                            AS statement FROM information_schema.tables
                                            WHERE table_schema = '".$wpdb->dbname."' AND table_name LIKE '".$prefix."%' limit 1;";
                $result = $wpdb->get_results($delete_all_tables_query);
                $result = $result[0];
                if (isset($result->statement)) {
                    $wpdb->query("SET FOREIGN_KEY_CHECKS = 0;");
                    $wpdb->query($result->statement);
                    $wpdb->query("SET FOREIGN_KEY_CHECKS = 1;");
                }
            } catch (\Exception $e) {
                $rules["mysql_connection"] = "required";
            }

            // Check if database is not empty
            $rules = [];
            $prefix_check = empty($prefix) ? '' : "  AND table_name LIKE '".$prefix."%'";
            $result = $wpdb->get_results("SELECT COUNT(DISTINCT `table_name`) as count FROM `information_schema`.`columns` WHERE `table_schema` = '".$wpdb->dbname."'".$prefix_check);
            $result = $result[0];
            if ($result->count > 0) {
                $rules['can_not_empty_database'] = 'required';
            }
            $validator = \Validator::make($request->all(), $rules);    
            if ($validator->fails()) {
                return redirect()->action('InstallController@databaseImport')
                    ->withErrors($validator)
                    ->withInput();
            }
            
            $request->session()->set('database_imported', true);
            $next_page = action('InstallController@cronJobs');
            // Run migrate
            artisan_migrate();

            // import database with prefix
            $this->importDatabase();

            // default date
            $date = \Carbon\Carbon::now();

            // Insert system urls
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='url_delivery_handler'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('url_delivery_handler', '".action('DeliveryController@notify', ['stype' => ''])."', '".$date."', '".$date."');");
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='url_unsubscribe'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('url_unsubscribe', '".action('CampaignController@unsubscribe', ['message_id' => 'MESSAGE_ID'])."', '".$date."', '".$date."');");
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='url_open_track'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('url_open_track', '".action('CampaignController@open', ['message_id' => 'MESSAGE_ID'])."', '".$date."', '".$date."');");
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='url_click_track'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('url_click_track', '".action('CampaignController@click', ['message_id' => 'MESSAGE_ID', 'url' => 'URL'])."', '".$date."', '".$date."');");
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='url_update_profile'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('url_update_profile', '".action('PageController@profileUpdateForm', ['list_uid' => 'LIST_UID', 'uid' => 'SUBSCRIBER_UID', 'code' => 'SECURE_CODE'])."', '".$date."', '".$date."');");
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='site_name'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('site_name', '', '".$date."', '".$date."');");
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='site_keyword'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('site_keyword', '', '".$date."', '".$date."');");
            $wpdb->query('DELETE FROM `'.$prefix."settings` WHERE name='site_description'");
            $wpdb->query('INSERT INTO `'.$prefix."settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                            ('site_description', '', '".$date."', '".$date."');");

            $request->session()->flash('alert-success', trans('messages.install.database_import.success'));
            return redirect()->away($next_page);
        }

        return view('install.database_import', [
            'database' => $database,
            'step' => $this->step($request),
            'current' => 3,
            'tables_exist' => $tables_exist,
        ]);
    }

    public function cronJobs(Request $request)
    {
        if ($this->step($request) < 5) {
            return redirect()->action('InstallController@database');
        }
        
        $respone = \Acelle\Library\Tool::cronjobUpdateController($request, $this);        
        if($respone == 'done') {
            return redirect()->action('InstallController@finishing');
        }
        
        return view('install.cron_jobs', $respone);
    }
    
    public function finishing(Request $request)
    {
        $next_page = action('InstallController@finish');
        artisan_config_cache();
        return redirect()->away($next_page);
    }

    public function finish(Request $request)
    {
        if ($this->step($request) < 6) {
            return redirect()->action('InstallController@database');
        }
        
        $request->session()->set('install_finish', true);
        
        $file_path = storage_path('installed');
        $file = fopen($file_path, 'w') or die('Unable to open file!');
        fwrite($file, '');
        fclose($file);

        return view('install.finish', [
            'step' => $this->step($request),
            'current' => 6,
        ]);
    }

    // Check for requirement when install app
    public function checkSystemCompatibility()
    {
        return [
            [
                'type' => 'requirement',
                'name' => 'PHP version',
                'check' => version_compare(PHP_VERSION, '5.5.9', '>='),
                'note' => 'PHP 5.5.9 or higher is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'Mysqli Extension',
                'check' => function_exists('mysqli_connect'),
                'note' => 'Mysqli Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'OpenSSL Extension',
                'check' => extension_loaded('openssl'),
                'note' => 'OpenSSL PHP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'Mbstring PHP Extension',
                'check' => extension_loaded('mbstring'),
                'note' => 'Mbstring PHP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PDO PHP extension',
                'check' => extension_loaded('pdo'),
                'note' => 'PDO PHP extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'Tokenizer PHP Extension',
                'check' => extension_loaded('tokenizer'),
                'note' => 'Tokenizer PHP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP Zip Archive',
                'check' => class_exists('ZipArchive', false),
                'note' => 'PHP Zip Archive is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'IMAP Extension',
                'check' => extension_loaded('imap'),
                'note' => 'PHP IMAP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP GD Library',
                'check' => (extension_loaded('gd') && function_exists('gd_info')),
                'note' => 'PHP GD Library is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP Fileinfo extension',
                'check' => extension_loaded('fileinfo'),
                'note' => 'PHP Fileinfo extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP CURL extension',
                'check' => extension_loaded('curl'),
                'note' => 'PHP CURL extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP XML extension',
                'check' => extension_loaded('xml'),
                'note' => 'PHP XML extension is required.',
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/app'),
                'check' => file_exists(base_path('/storage/app')) &&
                    is_dir(base_path('/storage/app')) &&
                    (is_writable(base_path('/storage/app'))),
                'note' => 'The directory must be writable by the web server.',
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/framework'),
                'check' => file_exists(base_path('/storage/framework')) && is_dir(base_path('/storage/framework')) && (is_writable(base_path('/storage/framework'))),
                'note' => 'The directory must be writable by the web server.',
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/logs'),
                'check' => file_exists(base_path('/storage/logs')) && is_dir(base_path('/storage/logs')) && (is_writable(base_path('/storage/logs'))),
                'note' => 'The directory must be writable by the web server.',
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/job'),
                'check' => file_exists(base_path('/storage/job')) && is_dir(base_path('/storage/job')) && (is_writable(base_path('/storage/job'))),
                'note' => 'The directory must be writable by the web server.',
            ],
            [
                'type' => 'permission',
                'name' => base_path('bootstrap/cache'),
                'check' => file_exists(base_path('/bootstrap/cache')) && is_dir(base_path('/bootstrap/cache')) && (is_writable(base_path('/bootstrap/cache'))),
                'note' => 'The directory must be writable by the web server.',
            ],
        ];
    }

    public function checkServerVar()
    {
        $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
        $missing = array();
        foreach ($vars as $var) {
            if (!isset($_SERVER[$var])) {
                $missing[] = $var;
            }
        }

        if (!empty($missing)) {
            return '$_SERVER does not have: '.implode(', ', $missing);
        }

        if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING'])) {
            return 'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.';
        }

        if (!isset($_SERVER['PATH_INFO']) && strpos($_SERVER['PHP_SELF'], $_SERVER['SCRIPT_NAME']) !== 0) {
            return 'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.';
        }

        return '';
    }

    public function checkCaptchaSupport()
    {
        if (function_exists('getimagesize')) {
            return '';
        }

        if (extension_loaded('imagick')) {
            $imagick = new Imagick();
            $imagickFormats = $imagick->queryFormats('PNG');
        }

        if (extension_loaded('gd')) {
            $gdInfo = gd_info();
        }

        if (isset($imagickFormats) && in_array('PNG', $imagickFormats)) {
            return '';
        } elseif (isset($gdInfo)) {
            if ($gdInfo['FreeType Support']) {
                return '';
            }

            return 'GD installed,<br />FreeType support not installed';
        }

        return 'GD or ImageMagick not installed';
    }

    public function importDatabase()
    {
        global $wpdb;
        
        // read and replace prefix
        $file_path = storage_path('install/database_init.sql');
        $file = fopen($file_path, 'r') or die('Unable to open file!');
        $sql = fread($file, filesize($file_path));
        $sql = str_replace('<<prefix>>', $wpdb->prefix . "mailfit_", $sql);
        fclose($file);

        // write file
        $file_path = storage_path('app/database_import.sql');
        $file = fopen($file_path, 'w') or die('Unable to open file!');
        fwrite($file, $sql);
        fclose($file);

        // import sql
        $file_path = storage_path('app/database_import.sql');

        return $this->importSql($file_path);
    }

    public function importSql($file_path)
    {
        global $wpdb;
        
        // Name of the file
        $filename = $file_path;

        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($filename);
        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                $wpdb->query($templine) or print 'Error performing query \'<strong>'.$templine.'\': '.$mysqli->connect_error.'<br /><br />';
                // Reset temp variable to empty
                $templine = '';
            }
        }
        
        unlink($file_path);
        
        return true;
    }
}
