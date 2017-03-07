<?php

namespace Acelle\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class WordPressServiceProvider extends ServiceProvider
{
    protected $wordpressAdminPath = '/../../../../wp-admin';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(DispatcherContract $events) {
        // Just get WP user after Acelle installed
        if (\File::exists(base_path('storage/installed'))) {
            // WordPress get current login user id
            $wp_user_id = get_current_user_id();
            
            // Check if there are logged in WordPress user
            if (!empty($wp_user_id)) {
                try {
                    $user = \Acelle\Model\User::getUserWithWordPressUserId($wp_user_id);
                    \Auth::login($user);
                    return;
                } catch (\PDOException $e) {
                    // Reload cache if cannot connect to the database
                    \Artisan::call('config:cache');
                    sleep(5);
                    
                    // reload page after reload cache
                    header("Refresh:0");
                    exit;
                } catch (\Exception $e) {
                }
            } else {
                \Auth::logout();
                redirect($this->wordpressAdminPath)->send();
            }
        }
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        // Path for wordpress bootstrap file
        $bootstrapFilePath = __DIR__.'/../../../../../wp-load.php';
        
        // Load wordpress bootstrap file
        if(\File::exists($bootstrapFilePath)) {
            if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
                define('WP_PLUGIN_DIR', 'none');
            }
            require_once $bootstrapFilePath;            
        } else throw new \RuntimeException('WordPress Bootstrap file not found!' . $bootstrapFilePath);
    }
}