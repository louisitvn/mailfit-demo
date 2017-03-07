<?php

/**
 * UserGroup class.
 *
 * Model class for user group
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

class UserGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'backend_access', 'frontend_access', 'options',
    ];

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
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

    /**
     * The rules for validation.
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
    );

    /**
     * Rules.
     *
     * @return array
     */
    public static function rules()
    {
        $rules = [
            'name' => 'required',
        ];

        $options = self::defaultOptions();
        foreach ($options as $type => $option) {
            foreach ($option as $name => $value) {
                if ($type == 'frontend' && $name == 'sending_quota_time') {
                    $rules['options.'.$type.'.'.$name] = 'required|numeric|min:1';
                } else {
                    $rules['options.'.$type.'.'.$name] = 'required';
                }                
            }
        }

        return $rules;
    }

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function users()
    {
        return $this->hasMany('Acelle\Model\User');
    }

    public function user_group_sending_servers()
    {
        return $this->hasMany('Acelle\Model\UserGroupSendingServer');
    }

    public function sending_servers()
    {
        return $this->belongsToMany('Acelle\Model\SendingServer', 'user_group_sending_servers');
    }

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $user = $request->user();
        $query = self::select('user_groups.*');

        if ($request->user()->getOption('backend', 'user_group_read') == 'own') {
            $query = $query->where('user_id', '=', $request->user()->id);
        }

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
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Update custom order
            UserGroup::getAll()->increment('custom_order', 1);
            $item->custom_order = 0;
        });
    }

    /**
     * Get select options.
     *
     * @return array
     */
    public static function getSelectOptions()
    {
        $options = self::getAll()->get()->map(function ($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });

        return $options;
    }

    /**
     * Default options for new groups.
     *
     * @return array
     */
    public static function defaultOptions()
    {
        return [
            'frontend' => [
                'list_max' => '-1',
                'subscriber_max' => '-1',
                'subscriber_per_list_max' => '-1',
                'segment_per_list_max' => '3',
                'campaign_max' => '-1',
                'sending_quota' => '-1',
                'sending_quota_time' => '1',
                'sending_quota_time_unit' => 'month',
                'max_process' => '1',
                'all_sending_servers' => 'yes',
                'max_size_upload_total' => '500',
                'max_file_size_upload' => '5',
                'unsubscribe_url_required' => 'yes',
            ],
            'backend' => [
                'user_group_read' => 'all',
                'user_group_create' => 'yes',
                'user_group_update' => 'all',
                'user_group_delete' => 'own',
                'user_read' => 'own',
                'user_create' => 'yes',
                'user_update' => 'own',
                'user_delete' => 'own',
                'user_switch' => 'own',
                'sending_server_read' => 'all',
                'sending_server_create' => 'yes',
                'sending_server_update' => 'own',
                'sending_server_delete' => 'own',
                'bounce_handler_read' => 'own',
                'bounce_handler_create' => 'yes',
                'bounce_handler_update' => 'own',
                'bounce_handler_delete' => 'own',
                'fbl_handler_read' => 'own',
                'fbl_handler_create' => 'yes',
                'fbl_handler_update' => 'own',
                'fbl_handler_delete' => 'own',
                'sending_domain_read' => 'own',
                'sending_domain_create' => 'yes',
                'sending_domain_update' => 'own',
                'sending_domain_delete' => 'own',
                'template_read' => 'own',
                'template_create' => 'yes',
                'template_update' => 'own',
                'template_delete' => 'own',
                'layout_read' => 'yes',
                'layout_update' => 'yes',
                'setting_general' => 'yes',
                'setting_sending' => 'yes',
                'setting_system_urls' => 'yes',
                'setting_access_when_offline' => 'yes',
                'language_read' => 'yes',
                'language_create' => 'yes',
                'language_update' => 'yes',
                'language_delete' => 'yes',
                'report_blacklist' => 'yes',
                'report_tracking_log' => 'yes',
                'report_bounce_log' => 'yes',
                'report_feedback_log' => 'yes',
                'report_click_log' => 'yes',
                'report_open_log' => 'yes',
                'report_unsubscribe_log' => 'yes',
            ],
        ];
    }

    /**
     * Backend roles.
     *
     * @return array
     */
    public static function backendPermissions()
    {
        return [
            'user_group' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
            ],
            'user' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'switch' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
            ],
            'sending_server' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
            ],
            'bounce_handler' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
            ],
            'fbl_handler' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
            ],
            'sending_domain' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
            ],
            'template' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'own', 'text' => trans('messages.own')],
                        ['value' => 'all', 'text' => trans('messages.all')],
                    ],
                ],
            ],
            'layout' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
            ],
            'setting' => [
                'general' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'sending' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'system_urls' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'access_when_offline' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
            ],
            'language' => [
                'read' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'create' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'update' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'delete' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
            ],
            'report' => [
                'blacklist' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'tracking_log' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'bounce_log' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'feedback_log' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'open_log' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'click_log' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
                'unsubscribe_log' => [
                    'options' => [
                        ['value' => 'no', 'text' => trans('messages.no')],
                        ['value' => 'yes', 'text' => trans('messages.yes')],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        if (empty($this->options)) {
            return self::defaultOptions();
        } else {
            $defaul_options = self::defaultOptions();
            $saved_options = json_decode($this->options, true);
            foreach($defaul_options as $x => $group) {
                foreach($group as $y => $option) {
                    if(isset($saved_options[$x][$y])) {
                        $defaul_options[$x][$y] = $saved_options[$x][$y];
                    }
                }
            }
            return $defaul_options;
        }
    }

    /**
     * Get option.
     *
     * @return string
     */
    public function getOption($cat, $name)
    {
        return $this->getOptions()[$cat][$name];
    }

    /**
     * Save options.
     *
     * @return array
     */
    public function saveOptions($options)
    {
        return true;
    }

    /**
     * Quota time unit options.
     *
     * @return array
     */
    public static function timeUnitOptions()
    {
        return [
            ['value' => 'minute', 'text' => trans('messages.minute')],
            ['value' => 'hour', 'text' => trans('messages.hour')],
            ['value' => 'day', 'text' => trans('messages.day')],
            ['value' => 'week', 'text' => trans('messages.week')],
            ['value' => 'month', 'text' => trans('messages.month')],
            ['value' => 'year', 'text' => trans('messages.year')],
        ];
    }

    /**
     * Get sending servers ids.
     *
     * @return array
     */
    public function getSendingServerIds()
    {
        $arr = [];
        foreach ($this->sending_servers as $server) {
            $arr[] = $server->uid;
        }

        return $arr;
    }

    /**
     * Update sending servers.
     *
     * @return array
     */
    public function updateSendingServers($servers)
    {
        $this->user_group_sending_servers()->delete();
        foreach ($servers as $key => $param) {
            if ($param['check']) {
                $server = SendingServer::findByUid($key);
                $row = new UserGroupSendingServer();
                $row->user_group_id = $this->id;
                $row->sending_server_id = $server->id;
                $row->fitness = $param['fitness'];
                $row->save();
            }
        }
    }

    /**
     * Multi process select options.
     *
     * @return array
     */
    public static function multiProcessSelectOptions()
    {
        $options = [['value' => 1, 'text' => trans('messages.one_single_process')]];
        for ($i = 2; $i < 101; ++$i) {
            $options[] = ['value' => $i, 'text' => $i];
        }

        return $options;
    }
    
    /**
     * Display sending servers count
     *
     * @return array
     */
    public function displaySendingServersCount()
    {
        if ($this->getOption("frontend","all_sending_servers") == 'yes') {
            return trans('messages.all');
        } else {
            return $this->user_group_sending_servers()->count();
        }
    }
}
