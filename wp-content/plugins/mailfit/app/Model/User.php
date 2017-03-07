<?php

/**
 * User class.
 *
 * Model class for user
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Acelle\Library\QuotaTrackerStd;
use Carbon\Carbon;
use Acelle\Library\Log as MailLog;

class User extends Authenticatable
{
    protected $quotaTracker;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'first_name', 'last_name', 'email', 'timezone', 'language_id', 'frontend_scheme', 'backend_scheme',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public static $rules = array(
        'user_group_id' => 'required',
        'email' => 'required|email',
        'first_name' => 'required',
        'last_name' => 'required',
        'timezone' => 'required',
        'language_id' => 'required',
        'password' => 'confirmed|min:5',
    );

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        return array(
            'email' => 'required|email|unique:users,email,'.$this->id.',id',
            'first_name' => 'required',
            'last_name' => 'required',
            'timezone' => 'required',
            'language_id' => 'required',
            'password' => 'confirmed|min:5',
        );
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function newRules()
    {
        return array(
            'user_group_id' => 'required',
            'email' => 'required|email|unique:users,email,'.$this->id.',id',
            'first_name' => 'required',
            'last_name' => 'required',
            'timezone' => 'required',
            'language_id' => 'required',
            'password' => 'required|confirmed|min:5',
        );
    }
    
    /**
     * The rules for validation via api.
     *
     * @var array
     */
    public function apiRules()
    {
        return array(
            'user_group_id' => 'required',
            'email' => 'required|email|unique:users,email,'.$this->id.',id',
            'first_name' => 'required',
            'last_name' => 'required',
            'timezone' => 'required',
            'language_id' => 'required',
            'password' => 'required|min:5',
        );
    }
    
    /**
     * The rules for validation via api.
     *
     * @var array
     */
    public function apiUpdateRules($request)
    {
        $arr = [];
        
        if(isset($request->email)) {
            $arr['email'] = 'required|email|unique:users,email,'.$this->id.',id';
        }
        if(isset($request->first_name)) {
            $arr['first_name'] = 'required';
        }
        if(isset($request->last_name)) {
            $arr['last_name'] = 'required';
        }
        if(isset($request->timezone)) {
            $arr['timezone'] = 'required';
        }
        if(isset($request->language_id)) {
            $arr['language_id'] = 'required';
        }
        if(isset($request->password)) {
            $arr['password'] = 'min:5';
        }
        
        return $arr;
    }

    /**
     * Find item by uid.
     *
     * @return object
     */
    public static function findByUid($uid)
    {
        return self::where('uid', '=', $uid)->first();
    }

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function contact()
    {
        return $this->belongsTo('Acelle\Model\Contact');
    }

    public function user()
    {
        return $this->hasOne('Acelle\Model\User');
    }

    public function userGroup()
    {
        return $this->belongsTo('Acelle\Model\UserGroup');
    }

    public function lists()
    {
        return $this->hasMany('Acelle\Model\MailList')->orderBy('created_at', 'desc');
    }

    public function language()
    {
        return $this->belongsTo('Acelle\Model\Language');
    }

    public function campaigns()
    {
        return $this->hasMany('Acelle\Model\Campaign')->orderBy('created_at', 'desc');
    }

    public function sentCampaigns()
    {
        return $this->hasMany('Acelle\Model\Campaign')->where('status', '=', 'done')->orderBy('created_at', 'desc');
    }

    public function subscribers()
    {
        return $this->hasMany('Acelle\Model\Subscriber');
    }

    public function logs()
    {
        return $this->hasMany('Acelle\Model\Log')->orderBy('created_at', 'desc');
    }
    
    public function systemJobs()
    {
        return $this->hasMany('Acelle\Model\SystemJob')->orderBy('created_at', 'desc');
    }

    public function trackingLogs()
    {
        return $this->hasMany('Acelle\Model\TrackingLog')->orderBy('created_at', 'asc');
    }
    
    public function automations()
    {
        return $this->hasMany('Acelle\Model\Automation');
    }

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (User::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // Add api token
            $item->api_token = str_random(60);
        });
    }

    /**
     * Display user name: first_name last_name.
     *
     * @var string
     */
    public function displayName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Upload and resize avatar.
     *
     * @var void
     */
    public function uploadImage($file)
    {
        $path = 'app/users/';
        $upload_path = storage_path($path);
        
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        $filename = 'avatar-'.$this->id.'.'.$file->getClientOriginalExtension();

        // save to server
        $file->move($upload_path, $filename);

        // create thumbnails
        $img = \Image::make($upload_path.$filename);
        $img->fit(120, 120)->save($upload_path.$filename.'.thumb.jpg');

        return $path.$filename;
    }

    /**
     * Get image thumb path.
     *
     * @var string
     */
    public function imagePath()
    {
        if (!empty($this->image) && !empty($this->id)) {
            return storage_path($this->image).'.thumb.jpg';
        } else {
            return '';
        }
    }

    /**
     * Get image thumb path.
     *
     * @var string
     */
    public function removeImage()
    {
        if (!empty($this->image) && !empty($this->id)) {
            $path = storage_path($this->image);
            if (is_file($path)) {
                unlink($path);
            }
            if (is_file($path.'.thumb.jpg')) {
                unlink($path.'.thumb.jpg');
            }
        }
    }

    /**
     * Get authenticate from file.
     *
     * @return string
     */
    public static function getAuthenticateFromFile()
    {
        $path = base_path('.authenticate');

        if (file_exists($path)) {
            $content = \File::get($path);
            $lines = explode("\n", $content);
            if (count($lines) > 1) {
                $demo = session()->get('demo');
                if (!isset($demo) || $demo == 'admin') {
                    return ['email' => $lines[0], 'password' => $lines[1]];
                } else {
                    return ['email' => $lines[0], 'password' => $lines[1]];
                }
            }
        }

        return ['email' => '', 'password' => ''];
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return User::select('*');
    }

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $user = $request->user();
        $query = self::select('users.*')
                        ->leftJoin('user_groups', 'user_groups.id', '=', 'users.user_group_id');

        if ($request->user()->getOption('backend', 'user_read') == 'own') {
            $query = $query->where('users.user_id', '=', $request->user()->id);
        }

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('users.first_name', 'like', '%'.$keyword.'%')
                        ->orWhere('users.email', 'like', '%'.$keyword.'%')
                        ->orWhere('user_groups.name', 'like', '%'.$keyword.'%')
                        ->orWhere('users.last_name', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;
        if (!empty($filters)) {
            if (!empty($filters['user_group_id'])) {
                $query = $query->where('users.user_group_id', '=', $filters['user_group_id']);
            }
        }

        return $query;
    }

    /**
     * Search items.
     *
     * @return collect
     */
    public static function search($request)
    {
        $query = self::filter($request);

        $query = $query->orderBy($request->sort_order, $request->sort_direction);

        return $query;
    }

    /**
     * Subscribers count by time.
     *
     * @return number
     */
    public static function subscribersCountByTime($begin, $end, $user_id = null, $list_id = null)
    {
        $query = \Acelle\Model\Subscriber::leftJoin('mail_lists', 'mail_lists.id', '=', 'subscribers.mail_list_id')
                                ->leftJoin('users', 'users.id', '=', 'mail_lists.user_id');

        if (isset($list_id)) {
            $query = $query->where('subscribers.mail_list_id', '=', $list_id);
        }
        if (isset($user_id)) {
            $query = $query->where('users.id', '=', $user_id);
        }

        $query = $query->where('subscribers.created_at', '<=', $end); //->where('subscribers.created_at', '>=', $begin);

        return $query->count();
    }

    /**
     * Get user setting.
     *
     * @return string
     */
    public function getOption($cat, $name)
    {
        return $this->userGroup->getOption($cat, $name);
    }

    /**
     * Get max list quota.
     *
     * @return number
     */
    public function maxLists()
    {
        // @todo: nhảm, nếu đã qui ước là -1 thì chỉ dùng -1, ∞ chỉ dùng để show bên view
        $count = $this->getOption('frontend', 'list_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Count user lists.
     *
     * @return number
     */
    public function listsCount()
    {
        return $this->lists()->count();
    }

    /**
     * Calculate list usage.
     *
     * @return number
     */
    public function listsUsage()
    {
        if ($this->maxLists() == '∞') {
            return 0;
        }
        if ($this->maxLists() == 0) {
            return 100;
        }

        return round((($this->listsCount() / $this->maxLists()) * 100), 2);
    }

    /**
     * Display calculate list usage.
     *
     * @return number
     */
    public function displayListsUsage()
    {
        if ($this->maxLists() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->listsUsage().'%';
    }

    /**
     * Get campaigns quota.
     *
     * @return number
     */
    public function maxCampaigns()
    {
        $count = $this->getOption('frontend', 'campaign_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Count user's campaigns.
     *
     * @return number
     */
    public function campaignsCount()
    {
        return $this->campaigns()->count();
    }

    /**
     * Calculate campaign usage.
     *
     * @return number
     */
    public function campaignsUsage()
    {
        if ($this->maxCampaigns() == '∞') {
            return 0;
        }
        if ($this->maxCampaigns() == 0) {
            return 100;
        }

        return round((($this->campaignsCount() / $this->maxCampaigns()) * 100), 2);
    }

    /**
     * Calculate campaign usage.
     *
     * @return number
     */
    public function displayCampaignsUsage()
    {
        if ($this->maxCampaigns() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->campaignsUsage().'%';
    }

    /**
     * Get subscriber quota.
     *
     * @return number
     */
    public function maxSubscribers()
    {
        $count = $this->getOption('frontend', 'subscriber_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Count user's subscribers.
     *
     * @return number
     */
    public function subscribersCount()
    {
        return $this->subscribers()->count();
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function subscribersUsage()
    {
        if ($this->maxSubscribers() == '∞') {
            return 0;
        }
        if ($this->maxSubscribers() == 0 || $this->subscribersCount() > $this->maxSubscribers()) {
            return 100;
        }

        return round((($this->subscribersCount() / $this->maxSubscribers()) * 100), 2);
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function displaySubscribersUsage()
    {
        if ($this->maxSubscribers() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->subscribersUsage().'%';
    }

    /**
     * Get user's quota.
     *
     * @return string
     */
    public function getSendingQuota()
    {
        // -1 indicate unlimited
        return $this->getOption('frontend', 'sending_quota');
    }

    /**
     * Check if user has access to ALL sending servers.
     *
     * @return boolean
     */
    public function allSendingServer()
    {
        $check = $this->getOption('frontend', 'all_sending_servers');
        return ($check == 'yes');
    }

    /**
     * Get user's sending quota.
     *
     * @return string
     */
    public function getSendingQuotaUsage()
    {
        $tracker = $this->getQuotaTracker();
        $tracker->check(); // renew the usage data (trigger the private shiftBy() function)
        return $tracker->usage();
    }

    /**
     * Get user's sending quota rate.
     *
     * @return string
     */
    public function getSendingQuotaUsagePercentage()
    {
        // @todo magic number
        if ($this->getSendingQuota() == -1) {
            return '0';
        }

        return round(($this->getQuotaTracker()->usagePercentage() * 100), 2);
    }

    /**
     * Check if user has used up all quota allocated.
     *
     * @return string
     */
    public function overQuota()
    {
        return !$this->getQuotaTracker()->check();
    }

    /**
     * Increment quota usage
     *
     * @return void
     */
    public function countUsage(Carbon $timePoint = null)
    {
        return $this->getQuotaTracker()->add();
    }

    /**
     * Get user's sending quota rate.
     *
     * @return string
     */
    public function displaySendingQuotaUsage()
    {
        if ($this->getSendingQuota() == -1) {
            return trans('messages.unlimited');
        }
        // @todo use percentage helper here
        return $this->getSendingQuotaUsagePercentage().'%';
    }

    /**
     * Get user's frontend scheme.
     *
     * @return string
     */
    public function getFrontendScheme()
    {
        if (!empty($this->frontend_scheme)) {
            return $this->frontend_scheme;
        } else {
            return \Acelle\Model\Setting::get('frontend_scheme');
        }
    }

    /**
     * Get user's frontend scheme.
     *
     * @return string
     */
    public function getBackendScheme()
    {
        if (!empty($this->backend_scheme)) {
            return $this->backend_scheme;
        } else {
            return \Acelle\Model\Setting::get('backend_scheme');
        }
    }

    /**
     * Color array.
     *
     * @return array
     */
    public static function colors($default)
    {
        return [
            ['value' => '', 'text' => trans('messages.system_default')],
            ['value' => 'blue', 'text' => trans('messages.blue')],
            ['value' => 'green', 'text' => trans('messages.green')],
            ['value' => 'brown', 'text' => trans('messages.brown')],
            ['value' => 'pink', 'text' => trans('messages.pink')],
            ['value' => 'grey', 'text' => trans('messages.grey')],
            ['value' => 'white', 'text' => trans('messages.white')],
        ];
    }

    /**
     * Get quota starting time
     *
     * @return array
     */
    public function getQuotaStartingTime() {
        $timeValue = $this->getOption('frontend', 'sending_quota_time');
        $timeUnit = $this->getOption('frontend', 'sending_quota_time_unit');
        
        // @todo: magic number here
        // @todo: why $timeValue == -1?
        // @todo: configuration setting for default Quota Starting Time
        if ($timeValue == '-1') {
            $start = \Carbon\Carbon::parse("1 month ago");
        } else {
            $start = \Carbon\Carbon::parse("{$timeValue} {$timeUnit} ago");
        }
        return $start;
    }

    /**
     * Get quota starting time
     *
     * @return array
     */
    public function getQuotaIntervalString() {
        $timeValue = $this->getOption('frontend', 'sending_quota_time');
        $timeUnit = $this->getOption('frontend', 'sending_quota_time_unit');
        return "{$timeValue} {$timeUnit}";
    }

    /**
     * Initialize the quota tracker
     *
     * @return array
     */
    public function renewQuotaTracker() {
        $start = $this->getQuotaStartingTime();

        // recent tracking logs
        // @todo: potential issue here, application fails in case $start is NULL
        $recent = $this->trackingLogs()->where('created_at', '>=', $start);
        
        // existing quota usage
        $stored = null;

        // load the tracker from DB (if exists)
        // then merge with the current tracking log data
        if ($this->quota) {
            try {
                $stored = unserialize($this->quota);
            } catch (\Exception $x) {
                // @TODO logging here
                MailLog::warning('Cannot retrieve user quota');
            }
        }

        // load the tracker from DB
        if (!is_null($stored) && !empty($stored)) {
            // retrieve the stored quota usage and merge it with actual (newer) usage
            $recent = $recent->where('created_at', '>', \Carbon\Carbon::createFromTimestamp(end($stored)));
            $series = collect($recent->get())->map(function($trackingLog) {
                return $trackingLog->created_at->timestamp;
            })->toArray();
            
            $series = array_merge($stored, $series);
        } else {
            $series = collect($recent->get())->map(function($trackingLog) {
                return $trackingLog->created_at->timestamp;
            })->toArray();
        }
        
        $this->getQuotaTracker()->renew($series);
    }

    /**
     * Initialize the quota tracker
     *
     * @return array
     */
    public function initQuotaTracker() {
        if (\Config::get('app.redis_enabled')) {
            $this->quotaTracker = new QuotaTrackerRedis(Redis::connection(), $this->getQuotaIntervalString(), $this->getSendingQuota());
            // @note: in case of Redis tracker, the following command must be issued manually
            //     $this->renewQuotaTracker();
            // Normally, Redis tracker is used in a multi-process context, the above command must be executed 
            //     by the parent process, before forking any child process
        } else {
            $this->quotaTracker = new QuotaTrackerStd($this->getQuotaIntervalString(), $this->getSendingQuota());
            // renew the quota data stored in the DB
            $this->renewQuotaTracker();
        }
    }

    /**
     * Get user's QuotaTracker
     *
     * @return array
     */
    public function getQuotaTracker() {
        if(!$this->quotaTracker) {
            $this->initQuotaTracker();
        }

        return $this->quotaTracker;
    }

    /**
     * Store the current quota usage info to DB
     *
     * @return array
     */
    public function saveQuotaUsageInfo() {
        $this->quota = serialize($this->getQuotaTracker()->getSeries());
        $this->save();
    }

    public function quotaDebug() {
        echo "During the last " . $this->getQuotaIntervalString() . ", Count: " . $this->getSendingQuotaUsage() . " / Total: " . $this->getSendingQuota() . "\n";
        echo "Now " . \Carbon\Carbon::now()->toDateTimeString() . "\n";
        foreach($this->getQuotaTracker()->getSeries() as $t) {
            echo \Carbon\Carbon::createFromTimestamp($t)->toDateTimeString() . " <-> " ;
        }
        echo "\n" . env('REDIS_ENABLED') . "\n";
    }
    
    /**
     * Get user corresponding with WordPress user id
     *
     * @return object
     */
    public static function getUserWithWordPressUserId($wp_user_id) {
        $user = \Acelle\Model\User::find($wp_user_id);
        
        // create user with wordpress if not exist
        if(!is_object($user)) {
            // Get WordPress user info
            $wp_user = get_user_by('id', $wp_user_id);
            
            $user = new User();
            $user->id = $wp_user_id;
            $user->email = $wp_user->user_email;
            
            // default user group with full access
            $user->user_group_id = 1;
            
            // default language - en
            $user->language_id = Language::getIsDefaultLanguage()->id;
            $user->save();
        }
        
        return $user;
    }
    
    /**
     * Get WordPress user information
     *
     * @return object
     */
    function getWPUser() {
        return get_userdata( $this->id );
    }
    
    /**
     * Get all user roles names
     *
     * @return array
     */
    public static function getWPUserRoles() {
        global $wp_roles;

        return $wp_roles->roles; 
    }
    
    /**
     * Get all user roles select options
     *
     * @return array
     */
    public static function getWPUserRoleSelectOptions() {
        $user_counts = count_users();
        $roles = User::getWPUserRoles();
        
        foreach($user_counts["avail_roles"] as $role_name => $role_count) {
            if(isset($roles[$role_name])) {
                $options[] = ['text' => $roles[$role_name]['name'] . " ($role_count)", 'value' => $role_name];
            } else if($role_count) {
                $options[] = ['text' => ucfirst(__($role_name)), 'value' => $role_name];
            }
        }
        
        return $options;
    }
    
    /**
     * Get user timezone
     *
     * @return string
     */
    public function getTimezone() {
        $timezone = get_option('timezone_string');
        $offset = get_option('gmt_offset');
        if(!empty($timezone)) {
            $result = $timezone;
        } else if(!empty($offset)) {
            $hours = floor($offset);
            $minutes = ($offset - $hours) * 60;
            $result = $hours . ":" . $minutes;
            if($offset >= 0) {
                $result = "+" . $result;
            }
        } else {
            $result = '+00:00';
        }
        return $result;
    }
    
    /**
     * Get not auto campaign.
     *
     * @return array
     */
    public function getNormalCampaigns()
    {
        return $this->campaigns()->where('is_auto', '=', false);
    }
    
    /**
     * Get user language code
     *
     * @return string
     */
    public function getLanguageCode() {
        return (is_object($this->language) ? $this->language->code : null);
    }
}
