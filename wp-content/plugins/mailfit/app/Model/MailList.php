<?php

/**
 * MailList class.
 *
 * Model class for log mail list
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

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

class MailList extends Model
{
    // Subscribers to import every time
    const IMPORT_STATUS_NEW = 'new';
    const IMPORT_STATUS_RUNNING = 'running';
    const IMPORT_STATUS_FAILED = 'failed';
    const IMPORT_STATUS_DONE = 'done';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'default_subject', 'from_email', 'from_name',
        'remind_message', 'send_to', 'email_daily', 'email_subscribe',
        'email_unsubscribe', 'send_welcome_email', 'unsubscribe_notification',
        'subscribe_confirmation'
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
        'from_email' => 'required|email',
        'from_name' => 'required',
        //'remind_message' => 'required',
        'contact.company' => 'required',
        'contact.url' => 'url',
        'email_subscribe' => 'regex:"^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4}[\W]*,{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4})[\W]*$"',
        'email_unsubscribe' => 'regex:"^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4}[\W]*,{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4})[\W]*$"',
        'email_daily' => 'regex:"^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4}[\W]*,{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4})[\W]*$"',
    );

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function fields()
    {
        return $this->hasMany('Acelle\Model\Field');
    }

    public function user()
    {
        return $this->belongsTo('Acelle\Model\User');
    }

    public function segments()
    {
        return $this->hasMany('Acelle\Model\Segment');
    }

    public function pages()
    {
        return $this->hasMany('Acelle\Model\Page');
    }

    public function page($layout)
    {
        return $this->pages()->where('layout_id', $layout->id)->first();
    }

    public function contact()
    {
        return $this->belongsTo('Acelle\Model\Contact');
    }

    public function subscribers()
    {
        return $this->hasMany('Acelle\Model\Subscriber');
    }

    public function campaigns()
    {
        return $this->hasMany('Acelle\Model\Campaign');
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
            while (MailList::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // Update custom order
            MailList::getAll()->increment('custom_order', 1);
            $item->custom_order = 0;
        });

        // Create uid when list created.
        static::created(function ($item) {
            //  Create list default fields
            $item->createDefaultFieds();
        });

        // detele
        static::deleted(function ($item) {
            //  Delete contact when list deleted
            $item->contact->delete();
            
            // Delete import jobs
            $item->importJobs()->delete();
            
            // Delete export jobs
            $item->exportJobs()->delete();
        });
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return self::select('*');
    }

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $user = $request->user();
        $query = self::where('user_id', '=', $user->id);

        // Keyword
        if (!empty(trim($request->keyword))) {
            $query = $query->where('name', 'like', '%'.$request->keyword.'%');
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
     * Find item by uid.
     *
     * @return object
     */
    public static function findByUid($uid)
    {
        return self::where('uid', '=', $uid)->first();
    }

    /**
     * Get all fields.
     *
     * @return object
     */
    public function getFields()
    {
        return $this->fields()->orderBy('custom_order');
    }

    /**
     * Create default fields for list.
     */
    public function createDefaultFieds()
    {
        $this->fields()->create([
                            'mail_list_id' => $this->id,
                            'type' => 'text',
                            'label' => trans('messages.email'),
                            'tag' => 'EMAIL',
                            'required' => true,
                            'visible' => true,
                        ]);

        $this->fields()->create([
                            'mail_list_id' => $this->id,
                            'type' => 'text',
                            'label' => __('Name'),
                            'tag' => \Acelle\Model\Field::formatTag(__('Name')),
                            'required' => false,
                            'visible' => true,
                        ]);
    }

    /**
     * Get email field.
     *
     * @return object
     */
    public function getEmailField()
    {
        return $this->getFieldByTag('EMAIL');
    }

    /**
     * Get field by tag.
     *
     * @return object
     */
    public function getFieldByTag($tag)
    {
        return $this->fields()->where('tag', '=', $tag)->first();
    }
    
    /**
     * Get field by tag.
     *
     * @return object
     */
    public function createIfNotExistsByTag($tag)
    {
        $field = $this->getFieldByTag($tag);
        if(!is_object($field)) {
            $field = $this->fields()->create([
                'mail_list_id' => $this->id,
                'type' => 'text',
                'label' => $tag,
                'tag' => \Acelle\Model\Field::formatTag($tag),
                'required' => false,
                'visible' => true,
            ]);
        }
        
        return $field;
    }

    /**
     * Get field by tag.
     *
     * @return object
     */
    public function getActiveSubscribers()
    {
        return $this->subscribers()->where('status', 'active')->get();
    }

    /**
     * Get field rules.
     *
     * @return object
     */
    public function getFieldRules()
    {
        $rules = [];
        foreach ($this->getFields as $field) {
            if ($field->tag == 'EMAIL') {
                $rules[$field->tag] = 'required|email';
            } elseif ($field->required) {
                $rules[$field->tag] = 'required';
            }
        }

        return $rules;
    }

    /**
     * Check if a email is exsit.
     *
     * @param string the email
     *
     * @return bool
     */
    public function checkExsitEmail($email)
    {
        $valid = !filter_var($email, FILTER_VALIDATE_EMAIL) === false &&
            !empty($email) &&
            $this->subscribers()->where('email', '=', $email)->count() == 0;

        return $valid;
    }
    
    /**
     * Check if a email is valid.
     *
     * @param string the email
     *
     * @return bool
     */
    public function checkValidEmail($email)
    {
        $valid = !filter_var($email, FILTER_VALIDATE_EMAIL) === false &&
            !empty($email);

        return $valid;
    }
    
     /**
     * Find subscribers with email.
     *
     * @param string the email
     *
     * @return bool
     */
    public function findByEmail($email)
    {
        return $this->subscribers()->where('email', '=', $email)->first();
    }

    /**
     * Get select options.
     *
     * @return array
     */
    public static function getSelectOptions($user = null)
    {
        $query = self::getAll();
        if (is_object($user)) {
            $query = $query->where('user_id', '=', $user->id);
        }
        $options = $query->orderBy('name')->get()->map(function ($item) {
            return ['value' => $item->uid, 'text' => $item->name.' ('.$item->subscribers()->count().' '.strtolower(trans('messages.subscribers')).')'];
        });

        return $options;
    }

    /**
     * Get segments select options.
     *
     * @return array
     */
    public function getSegmentSelectOptions()
    {
        $options = $this->segments->map(function ($item) {
            return ['value' => $item->uid, 'text' => $item->name.' ('.$item->subscribers()->count().' '.strtolower(trans('messages.subscribers')).')'];
        });

        return $options;
    }

    /**
     * Count unsubscribe.
     *
     * @return array
     */
    public function unsubscribeCount()
    {
        return $this->subscribers()->where('status', '=', 'unsubscribed')->count();
    }

    /**
     * Unsubscribe rate.
     *
     * @return array
     */
    public function unsubscribeRate()
    {
        if ($this->subscribers()->count() == 0) {
            return '#';
        }

        return round(($this->unsubscribeCount() / $this->subscribers()->count()) * 100, 1);
    }

    /**
     * Count unsubscribe.
     *
     * @return array
     */
    public function subscribeCount()
    {
        return $this->subscribers()->where('status', '=', 'subscribed')->count();
    }

    /**
     * Unsubscribe rate.
     *
     * @return array
     */
    public function subscribeRate()
    {
        if ($this->subscribers()->count() == 0) {
            return '#';
        }

        return round(($this->subscribeCount() / $this->subscribers()->count()) * 100, 1);
    }

    /**
     * Count unsubscribe.
     *
     * @return array
     */
    public function unconfirmedCount()
    {
        return $this->subscribers()->where('status', '=', 'unconfirmed')->count();
    }
    
    /**
     * Count blacklisted.
     *
     * @return array
     */
    public function blacklistedCount()
    {
        return $this->subscribers()->where('status', '=', 'blacklisted')->count();
    }
    
    /**
     * Count blacklisted.
     *
     * @return array
     */
    public function spamReportedCount()
    {
        return $this->subscribers()->where('status', '=', 'spam-reported')->count();
    }
    
    /**
     * Count by status.
     *
     * @return array
     */
    public static function subscribersCountByStatus($status, $user=null)
    {
        $query = \Acelle\Model\Subscriber::where('subscribers.status', '=', $status);
        
        if(isset($user) && $user->getOption('backend', 'user_read') != 'all') {
            $query = $query->join('mail_lists', 'mail_lists.id', '=', 'subscribers.mail_list_id')
                            ->where("mail_lists.user_id", "=", $user->id);
        }
        
        return $query->count();
    }

    /**
     * Add customer action log.
     */
    public function log($name, $user, $add_datas = [])
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
        ];

        $data = array_merge($data, $add_datas);

        Log::create([
            'user_id' => $user->id,
            'type' => 'list',
            'name' => $name,
            'data' => json_encode($data),
        ]);
    }

    /**
     * url count.
     */
    public function urlCount()
    {
        $query = CampaignLink::join('campaigns', 'campaigns.id', '=', 'campaign_links.campaign_id')
            ->where('campaigns.default_mail_list_id', '=', $this->id);
        
        return $query->count();
    }

    /**
     * Open count.
     */
    public function openCount()
    {
        $query = OpenLog::join('tracking_logs', 'tracking_logs.message_id', '=', 'open_logs.message_id')
            ->whereIn('tracking_logs.subscriber_id', function ($query) {
                $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
            });

        return $query->count();
    }

    /**
     * Open count.
     */
    public function clickCount()
    {
        $query = ClickLog::join('tracking_logs', 'tracking_logs.message_id', '=', 'click_logs.message_id')
            ->whereIn('tracking_logs.subscriber_id', function ($query) {
                $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
            });

        return $query->distinct('url')->count('url');
    }

    /**
     * Open count.
     */
    public function openUniqCount()
    {
        $query = OpenLog::join('tracking_logs', 'tracking_logs.message_id', '=', 'open_logs.message_id')
            ->whereIn('tracking_logs.subscriber_id', function ($query) {
                $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
            });

        return $query->distinct('subscriber_id')->count('subscriber_id');
    }

    /**
     * Tracking count.
     */
    public function trackingCount()
    {
        $query = TrackingLog::whereIn('tracking_logs.subscriber_id', function ($query) {
            $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
        });

        return $query->count();
    }

    /**
     * Count open rate.
     *
     * @return number
     */
    public function openRate()
    {
        if ($this->trackingCount() == 0) {
            return 0;
        }

        return round(($this->openCount() / $this->trackingCount()) * 100, 0);
    }

    /**
     * Count open uniq rate.
     *
     * @return number
     */
    public function openUniqRate()
    {
        if ($this->trackingCount() == 0) {
            return 0;
        }
        
        return round(($this->openUniqCount() / $this->trackingCount()) * 100, 0);
    }

    /**
     * Count click rate.
     *
     * @return number
     */
    public function clickRate()
    {
        if ($this->urlCount() == 0) {
            return 0;
        }
        
        return round(($this->clickCount() / $this->urlCount()) * 100, 0);
    }

    /**
     * Get other lists.
     *
     * @return number
     */
    public function otherLists()
    {
        return \Auth::user()->lists()->where('id', '!=', $this->id)->get();
    }
    
    /**
     * Get name with subscrbers count.
     *
     * @return number
     */
    public function longName()
    {
        return $this->name.' - '.$this->subscribers()->count().' '.trans('messages.'.\Acelle\Library\Tool::getPluralPrase('subscriber', $this->subscribers()->count())).'';
    }
    
    /**
     * Copy new list.
     */
    public function copy($name)
    {
        $copy = $this->replicate();
        $copy->name = $name;
        $copy->created_at = \Carbon\Carbon::now();
        $copy->updated_at = \Carbon\Carbon::now();
        $copy->custom_order = 0;
        $copy->save();
        
        // Contact
        if (is_object($this->contact)) {
            $new_contact = $this->contact->replicate();
            $new_contact->save();
            
            // update contact
            $copy->contact_id = $new_contact->id;
            $copy->save();
        }

        // Remove default fields
        $copy->fields()->delete();
        // Fields
        foreach ($this->fields as $field) {
            $new_field = $field->replicate();
            $new_field->mail_list_id = $copy->id;
            $new_field->save();
        }
    }
    
    /**
     * Get import jobs.
     *
     * @return number
     */
    public function importJobs()
    {
        return \Acelle\Model\SystemJob::where("name","=","Acelle\Jobs\ImportSubscribersJob")
            ->where("data","like", "%\"mail_list_uid\":\"" . $this->uid . "\"%");            
    }
    
    /**
     * Get last export job.
     *
     * @return number
     */
    public function getLastImportJob()
    {
        return $this->importJobs()
            ->orderBy("created_at","DESC")
            ->first();
    }
    
    /**
     * Get WP import jobs.
     *
     * @return number
     */
    public function importWPJobs()
    {
        return \Acelle\Model\SystemJob::where("name","=","Acelle\Jobs\ImportWPUsersJob")
            ->where("data","like", "%\"mail_list_uid\":\"" . $this->uid . "\"%");            
    }
    
    /**
     * Get last WP export job.
     *
     * @return number
     */
    public function getLastWPImportJob()
    {
        return $this->importWPJobs()
            ->orderBy("created_at","DESC")
            ->first();
    }
    
    /**
     * Get export jobs.
     *
     * @return number
     */
    public function exportJobs()
    {
        return \Acelle\Model\SystemJob::where("name","=","Acelle\Jobs\ExportSubscribersJob")
            ->where("data","like", "%\"mail_list_uid\":\"" . $this->uid . "\"%");            
    }
    
    /**
     * Get last export job.
     *
     * @return number
     */
    public function getLastExportJob()
    {
        return $this->exportJobs()
            ->orderBy("created_at","DESC")
            ->first();
    }
    
    /**
     * Get last export log file.
     *
     * @return string file path
     */
    public function getLastImportLog() {
        $data = json_decode($this->getLastImportJob()->data, true);
        return $data['log'];
    }
    
    /**
     * Import subscribers.
     *
     * @return void
     */
    public function importWPUsers($user, $system_job, $path, $roles, $update_exists)
    {
        
        $num_per_time = 1;
        $users = [];
        $time = 0;
        
        // count total user in WP
        $total = 0;
        $counts = count_users();
        foreach($counts['avail_roles'] as $role => $count) {
            if(in_array($role, $roles)) {
                $total += $count;
            }
        }
        
        $success = 0;
        $error = 0;
        
        //try {
            do {
                $wp_users = get_users([
                    'role__in' => $roles,
                    'offset' => $time*$num_per_time,
                    'number' => $num_per_time,
                ]);
                
                // Prepair bulk insert data
                foreach($wp_users as $wp_user) {
                    // create field for WP user imported
                    $field = $this->createIfNotExistsByTag(__('Name'));
                    
                    $exists = $this->findByEmail($wp_user->data->user_email);                    
                    // create subscribers
                    if ($this->checkValidEmail($wp_user->data->user_email) && !is_object($exists)) {
                        // create new subscriber
                        $subscriber = new Subscriber([
                            'mail_list_id' => $this->id,
                            'email' => $wp_user->data->user_email
                        ]);
                        $subscriber->status = 'subscribed';
                        $subscriber->user_id = $this->user_id;
                        $subscriber->save();
                        
                        //
                        $subscriber->setField($this->getEmailField(), $wp_user->data->user_email);
                    }
                    
                    // update field if new subscriber or exists with update exits option equal ys
                    if(isset($subscriber)) {
                        $subscriber->setField($field, $wp_user->data->display_name);
                    }
                    if(is_object($exists) && $update_exists == 'yes') {                            
                        $exists->setField($field, $wp_user->data->display_name);
                    }
                    
                    $success++;
                }
                
                $content_cache = trans('messages.import_export_statistics_line', [
                    'total' => $total,
                    'processed' => $success + $error,
                    'success' => $success,
                    'error' => $error,
                ]);        
                // update system job
                $system_job->updateData([
                    "status" => "running",
                    "message" => $content_cache,
                    "total" => $total,
                    "success" => $success,
                    "error" => $error,
                    "percent" => round((($success + $error) / $total) * 100, 0)
                ]);
                
                $time++;
            } while (count($wp_users) > 0);
        //} catch(\Exception $e) {
        //    
        //}
        
        $content_cache = trans('messages.import_export_statistics_line', [
            'total' => $total,
            'processed' => $success + $error,
            'success' => $success,
            'error' => $error,
        ]);        
        // update system job
        $system_job->updateData([
            "status" => "done",
            "message" => $content_cache,
            "total" => $total,
            "success" => $success,
            "error" => $error,
            "percent" => 100
        ]);
        
        // Action Log
        $this->log('import_success', $user, ['count' => $success, 'error' => $error]);
    }
    
    /**
     * Export subscribers.
     *
     * @return void
     */
    public static function export($list, $user, $job)
    {
        // Info from job
        $systemJob = $job->getSystemJob();        
        $directory = $job->getPath();
        
        $file_path = $directory.'data.csv';
        
        // Import to database
        $total = $list->subscribers()->count();
        $success = 0;
        $error = 0;
        $lines_per_second = 1;        
        $headers = [];
        foreach ($list->getFields as $key => $field) {
            $headers[] = $field->tag;
        }
        $headers = implode(',', $headers);
        
        // write csv
        $myfile = file_put_contents($file_path, $headers.PHP_EOL , FILE_APPEND | LOCK_EX);
        
        $num = 100;
        for($page = 0; $page <= ceil($total/$num); $page++) { // ceil($total/$num)
            $data = [];
            foreach ($list->subscribers()->skip($page*$num)->take($num)->get() as $key => $item) {
                $cols = [];
                foreach ($list->fields as $key2 => $field) {
                    $value = $item->getValueByField($field);
                    $cols[] = $value;
                }
                $data[] = implode(',', $cols);

                ++$success;
            }
            
            // write csv
            $myfile = file_put_contents($file_path, implode("\r\n", $data).PHP_EOL , FILE_APPEND | LOCK_EX);
            
            $content_cache = trans('messages.import_export_statistics_line', [
                'total' => $total,
                'processed' => $success + $error,
                'success' => $success,
                'error' => $error,
            ]);
            
            // update system job
            $systemJob->data = json_encode([
                "mail_list_uid" => $list->uid,
                "user_id" => $user->id,
                "status" => "running",
                "message" => $content_cache,
                "total" => $total,
                "success" => $success,
                "error" => $error,
                "percent" => round((($success + $error) / $total) * 100, 0)
            ]);
            $systemJob->save();
        }
        
        $content_cache = trans('messages.import_export_statistics_line', [
            'total' => $total,
            'processed' => $success + $error,
            'success' => $success,
            'error' => $error,
        ]);
        
        // update system job
        $systemJob->data = json_encode([
            "mail_list_uid" => $list->uid,
            "user_id" => $user->id,
            "status" => "done",
            "message" => $content_cache,
            "total" => $total,
            "success" => $success,
            "error" => $error,
            "percent" => 100
        ]);
        $systemJob->save();
        
        // Action Log
        $list->log('export_success', $user, ['count' => $success, 'error' => $error]);
    }
    
    /**
     * Send subscription confirmation email to subscriber.
     *
     * @return void
     */
    public function sendSubscriptionConfirmationEmail($subscriber) {
        $list = $this;
        
        $layout = \Acelle\Model\Layout::where('alias', 'sign_up_confirmation_email')->first();
        $send_page = \Acelle\Model\Page::findPage($list, $layout);
        $send_page->renderContent(null, $subscriber);
        $send_page->sendMail($subscriber, trans('messages.'.$layout->alias.'.real'));
    }
    
    /**
     * Send subscription confirmation email to subscriber.
     *
     * @return void
     */
    public function sendSubscriptionWelcomeEmail($subscriber) {
        $list = $this;
        
        $layout = \Acelle\Model\Layout::where('alias', 'sign_up_welcome_email')->first();
        $send_page = \Acelle\Model\Page::findPage($list, $layout);
        $send_page->renderContent(null, $subscriber);
        $send_page->sendMail($subscriber, trans('messages.'.$layout->alias.'.real'));
    }
    
    /**
     * Send unsubscription goodbye email to subscriber.
     *
     * @return void
     */
    public function sendUnsubscriptionNotificationEmail($subscriber) {
        $list = $this;
        
        $layout = \Acelle\Model\Layout::where('alias', 'unsubscribe_goodbye_email')->first();
        $send_page = \Acelle\Model\Page::findPage($list, $layout);
        $send_page->renderContent(null, $subscriber);
        $send_page->sendMail($subscriber, trans('messages.'.$layout->alias.'.real'));
    }

    /**
     * Get country name.
     *
     * @return string
     */
    public function countryName() {
        return (is_object($this->contact->country) ? $this->contact->country->name : '');
    }
    
    /** 
     * Get date | datetime fields.
     *
     * @return array
     */
    public function getDateFields()
    {
        return $this->getFields()->whereIn('type', ['date', 'datetime'])->get();
    }
    
    /**
     * Get subscriber's fields select options.
     *
     * @return array
     */
    public function getSubscriberFieldSelectOptions()
    {
        $options = [];
        $options[] = ['text' => trans('messages.subscriber_subscription_date'), 'value' => 'subscription_date'];
        foreach ($this->getDateFields() as $field) {
            $options[] = ['text' => trans('messages.subscriber_s_field', ['name' => $field->label]), 'value' => $field->uid];
        }

        return $options;
    }

    /**
     * Read a CSV file, returning the meta information
     *
     * @param string file path
     * @return array [$headers, $availableFields, $lineCount, $results]
     */
    public function getRemainingAddSubscribersQuota()
    {
        $max = $this->user->getOption('frontend', 'subscriber_max');
        $maxPerList = $this->user->getOption('frontend', 'subscriber_per_list_max');
        
        $remainingForList = $maxPerList - $this->reload()->subscribers->count();
        $remaining = $max - $this->reload()->user->subscribers->count();

        $result = ($remainingForList > $remaining) ? $remaining : $remainingForList;
        return ($result > 0) ? $result : 0;
    }

    /**
     * Read a CSV file, returning the meta information
     *
     * @param string file path
     * @return array [$headers, $availableFields, $lineCount, $results]
     */
    private function readCsv($file)
    {
        try {
            // Fix the problem with MAC OS's line endings
            if (!ini_get("auto_detect_line_endings")) {
                ini_set("auto_detect_line_endings", '1');
            }

            // Read CSV files
            $lineCount = line_count($file) - 1; // do not count the header
            $reader = \League\Csv\Reader::createFromPath($file);
            $headers = array_map('strtolower', $reader->fetchOne());
            $fields = collect($this->fields)->map(function($field) { return strtolower($field->tag); })->toArray();

            // list's fields found in the input CSV
            $availableFields = array_intersect($headers, $fields);

            // split the entire list into smaller batches
            $results = $reader->fetchAssoc($headers);

            return [$headers, $availableFields, $lineCount, $results];
        } catch (\Exception $ex) {
            // @todo: translation here
            throw new \Exception("Invalid headers. Original error message is: " . $ex->getMessage());
        }
    }
    
    /**
     * Validate imported file's headers
     *
     * @param headers
     * @return true or throw an exception
     */
    private function validateCsvHeader($headers) {
        // @todo: validation rules required here, currently hard-coded
        $missing = array_diff(['email'], $headers);
        if (!empty($missing)) {
            // @todo: I18n is required here
            throw new \Exception(trans('messages.import_missing_header_field', ['fields' => implode(', ', $missing)]) );
        }
        return true;
    }

    /**
     * Validate imported record
     *
     * @param headers
     * @return boolean whether or not the record is valid
     */
    private function validateCsvRecord($record) {
        //@todo: failed validate should affect the count showing up on the UI (currently, failed is also counted as success)
        $validator = Validator::make(
            $record, 
            Subscriber::$rules,
            ['email' => 'invalid email address']
        );

        return [$validator->passes(), $validator->errors()->all()];
    }
    
    /**
     * Import subscriber from a CSV file
     *
     * @param string original value
     * @return string quoted value
     * @todo: use MySQL escape function to correctly escape string with astrophe
     */
    public function import2($file, $user, $system_job)
    {
        $processed_count = 0;
        $logger = $system_job->getLogger();
        $logger->info(trans('messages.Start_importing_for_list_uid', ['uid' => $this->uid]));
        
        try {
            // init the status
            $system_job->updateStatus([
                'status' => self::IMPORT_STATUS_RUNNING,
            ]);

            // Read CSV files
            list($headers, $availableFields, $lineCount, $results) = $this->readCsv($file);

            // validate headers, check for required fields
            // throw an exception in case of error
            $this->validateCsvHeader($availableFields);

            // update status, line count
            $system_job->updateStatus([ 'total' => $lineCount ]);

            // process by batches
            each_batch($results, config('app.import_batch_size'), true, function($batch) use ($logger, $availableFields, &$user, &$processed_count, &$system_job) {
                // increment count
                $processed_count += sizeof($batch);
                
                // authorization
                if (!$user->can('addMoreSubscribers', [$this, config('app.import_batch_size')])) {
                    // If use cannot create ANY other subscribers
                    if (!$user->can('addMoreSubscribers', [$this, 1])) {
                        throw new \Exception(trans('messages.error_add_max_quota'));
                    } else {
                        $batch = array_slice($batch, 0, $this->getRemainingAddSubscribersQuota());
                    }
                }

                // processing for every batch, 
                // using transaction to only commit at the end of the batch execution
                DB::beginTransaction();

                // create a temporary table containing the input subscribers
                $tmpTable = table('__tmp_subscribers');
                // @todo: hard-coded charset and COLLATE
                $tmpFields = implode(',', array_map(function($field) { return "`{$field}` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci"; }, $availableFields));
                DB::statement("DROP TEMPORARY TABLE IF EXISTS {$tmpTable};
                               CREATE TEMPORARY TABLE {$tmpTable}({$tmpFields});");

                // Insert subscriber fields from the batch to the temporary table
                // extract only fields whose name matches TAG NAME of MailList
                $data = collect($batch)->map(function($r) use ($availableFields) {
                    return array_only($r, $availableFields);
                })->toArray();

                // make the import data table unique by email 
                $data = array_unique_by($data, function($r) {
                    return $r['email'];
                });

                // validate amd remove invalid records
                $data = array_where($data, function($key, $value) use ($logger) {
                    list($valid, $errors) = $this->validateCsvRecord($value);
                    if(!$valid) {
                        $logger->warning($value['email'].": ".implode(", ", $errors));
                    }
                    return $valid;
                });
                DB::table('__tmp_subscribers')->insert($data);

                // Insert new subscribers from temp table to the main table
                DB::statement("INSERT INTO " . table('subscribers') . "(uid, mail_list_id, user_id, email, status, subscription_type, created_at, updated_at) 
                               SELECT UUID_SHORT(), " . $this->id . ", " . $user->id . ", uniq.email, " . db_quote(Subscriber::STATUS_SUBSCRIBED) . ", " . db_quote(Subscriber::SUBSCRIPTION_TYPE_IMPORTED) . ", NOW(), NOW()
                               FROM (SELECT tmp.email FROM {$tmpTable} tmp LEFT JOIN " . table('subscribers') . " main ON (tmp.email = main.email AND main.mail_list_id = {$this->id}) WHERE main.email IS NULL) uniq");

                // Insert subscribers' custom fields to the fields table
                DB::statement("DELETE FROM " . table('subscriber_fields') . " WHERE subscriber_id IN (SELECT main.id FROM " . table('subscribers') . " main JOIN {$tmpTable} tmp ON main.email = tmp.email WHERE mail_list_id = " . $this->id . ")");
                foreach($availableFields as $field) {
                    $sql = "INSERT INTO " . table('subscriber_fields') . "(subscriber_id, field_id, value, created_at, updated_at)
                    SELECT t.subscriber_id, f.id, t.`{$field}`, NOW(), NOW()
                    FROM (SELECT main.id AS subscriber_id, tmp.{$field} FROM " . table('subscribers') . " main JOIN {$tmpTable} tmp ON tmp.email = main.email WHERE main.mail_list_id = " . $this->id . ") t
                    JOIN " . table('fields') . " f ON f.tag = '{$field}' AND f.mail_list_id = " . $this->id;
                    DB::statement($sql);
                }

                // update status, finish one batch
                $system_job->updateStatus([ 'processed' => $processed_count ]);

                // Cleanup
                DB::statement("DROP TEMPORARY TABLE IF EXISTS {$tmpTable};");

                // Actually write to the database
                DB::commit();
            });

            // update status, finish all batches
            $system_job->updateStatus([ 'status' => self::IMPORT_STATUS_DONE, 'total' => $processed_count ]);

            // action Log
            $this->log('import_success', $user, ['count' => $processed_count, 'error' => '']);
            $logger->info(trans('messages.Finish_importing_for_list_uid', ['uid' => $this->uid]));
        } catch (\Exception $e) {
            // finish the transaction
            DB::rollBack();

            // write to job's logger
            $logger->error($e->getMessage());

            // update job status
            $system_job->updateStatus([
                'status' => self::IMPORT_STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            // Action Log
            $this->log('import_max_error', $user, ['count' => $processed_count]);
        }
    }

    /**
     * Reload mail list information
     *
     * @return object mail list model
     * @todo why reload() is needed?
     */
    public function reload() {
        return self::find($this->id);
    }
}
