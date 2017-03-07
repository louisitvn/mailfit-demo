<?php

/**
 * Segment class.
 *
 * Model class for list segment
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

class Segment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'matching',
    ];

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
        'matching' => 'required',
    );

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function mailList()
    {
        return $this->belongsTo('Acelle\Model\MailList');
    }

    public function segmentConditions()
    {
        return $this->hasMany('Acelle\Model\SegmentCondition');
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
            while (Segment::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
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
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $query = self::select('segments.*')->where("segments.mail_list_id", "=", $list->id);

        return $query->distinct();
    }

    /**
     * Get all languages.
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
     * Get type options.
     *
     * @return options
     */
    public static function getTypeOptions()
    {
        return [
            ['text' => trans('messages.all'), 'value' => 'all'],
            ['text' => trans('messages.any'), 'value' => 'any'],
        ];
    }

    /**
     * Get operators.
     *
     * @return options
     */
    public static function operators()
    {
        return [
            ['text' => trans('messages.equal'), 'value' => 'equal'],
            ['text' => trans('messages.not_equal'), 'value' => 'not_equal'],
            ['text' => trans('messages.contains'), 'value' => 'contains'],
            ['text' => trans('messages.not_contains'), 'value' => 'not_contains'],
            ['text' => trans('messages.starts'), 'value' => 'starts'],
            ['text' => trans('messages.ends'), 'value' => 'ends'],
            ['text' => trans('messages.not_starts'), 'value' => 'not_starts'],
            ['text' => trans('messages.not_ends'), 'value' => 'not_ends'],
            ['text' => trans('messages.greater'), 'value' => 'greater'],
            ['text' => trans('messages.less'), 'value' => 'less'],
            ['text' => trans('messages.blank'), 'value' => 'blank'],
            ['text' => trans('messages.not_blank'), 'value' => 'not_blank'],
        ];
    }
    
    /**
     * Get subscribers conditions.
     *
     * @return collect
     */
    public function getSubscribersConditions()
    {
        $conditions = array();
        foreach ($this->segmentConditions as $condition) {
            $keyword = $condition->value;
            $keyword = str_replace('[EMPTY]', '', $keyword);
            $keyword = str_replace('[DATETIME]', date('Y-m-d H:i:s'), $keyword);
            $keyword = str_replace('[DATE]', date('Y-m-d'), $keyword);

            $keyword = trim(strtolower($keyword));
            switch ($condition->operator) {
                case 'equal':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) = '".$keyword."'";
                    break;
                case 'not_equal':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) != '".$keyword."'";
                    break;
                case 'contains':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) LIKE '%".$keyword."%'";
                    break;
                case 'not_contains':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) NOT LIKE '%".$keyword."%'";
                    break;
                case 'starts':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) LIKE '".$keyword."%'";
                    break;
                case 'ends':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) LIKE '%".$keyword."'";
                    break;
                case 'greater':
                    $cond = \DB::getTablePrefix()."subscriber_fields.value > '".$keyword."'";
                    break;
                case 'less':
                    $cond = \DB::getTablePrefix()."subscriber_fields.value < '".$keyword."'";
                    break;
                case 'not_starts':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) NOT LIKE '".$keyword."%'";
                    break;
                case 'not_ends':
                    $cond = 'LOWER('.\DB::getTablePrefix()."subscriber_fields.value) NOT LIKE '%".$keyword."'";
                    break;
                case 'not_blank':
                    $cond = '(LOWER('.\DB::getTablePrefix()."subscriber_fields.value) != '' AND LOWER(".\DB::getTablePrefix().'subscriber_fields.value) IS NOT NULL)';
                    break;
                case 'blank':
                    $cond = '(LOWER('.\DB::getTablePrefix()."subscriber_fields.value) = '' OR LOWER(".\DB::getTablePrefix().'subscriber_fields.value) IS NULL)';
                    break;
                default:

            }

            $conditions[] = \DB::getTablePrefix().'subscribers.id IN (SELECT '.\DB::getTablePrefix().'subscriber_fields.subscriber_id FROM '.\DB::getTablePrefix().'subscriber_fields WHERE ('.\DB::getTablePrefix().'subscriber_fields.field_id = '.$condition->field_id.' AND '.$cond.'))';
        }

        //return $conditions;
        if ($this->matching == 'any') {
            $conditions = implode(' OR ', $conditions);
        } else {
            $conditions = implode(' AND ', $conditions);
        }
        
        return $conditions;
    }

    /**
     * Get all subscribers belongs to the segment.
     *
     * @return collect
     */
    public function subscribers($request = null)
    {
        $query = \Acelle\Model\Subscriber::select('subscribers.*')
                    ->where('subscribers.mail_list_id', $this->mail_list_id);

        $conditions = $this->getSubscribersConditions();

        if (!empty($conditions)) {
            $query = $query->whereRaw($conditions);
        }

        // filters
        if (isset($request)) {
            $query = Subscriber::filter($query, $request);
            $query = $query->orderBy($request->sort_order, $request->sort_direction);
        }

        return $query->distinct();
    }

    /**
     * Add customer action log.
     */
    public function log($name, $user, $add_datas = [])
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'list_id' => $this->mail_list_id,
            'list_name' => $this->mailList->name,
        ];

        $data = array_merge($data, $add_datas);

        Log::create([
            'user_id' => $user->id,
            'type' => 'segment',
            'name' => $name,
            'data' => json_encode($data),
        ]);
    }
}
