<?php

/**
 * SendingDomain class.
 *
 * Model class for sending domains
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

class SendingDomain extends Model
{
    const DKIM_FULL_SELECTOR = 'mailer._domainkey';

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
        $query = self::select('sending_domains.*');

        if ($request->user()->getOption('backend', 'sending_domain_read') == 'own') {
            $query = $query->where('sending_domains.user_id', '=', $request->user()->id);
        }

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('sending_domains.name', 'like', '%'.$keyword.'%')
                        ->orWhere('sending_domains.type', 'like', '%'.$keyword.'%')
                        ->orWhere('sending_domains.host', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;
        if (!empty($filters)) {
            if (!empty($filters['type'])) {
                $query = $query->where('sending_domains.type', '=', $filters['type']);
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
     * Find item by uid.
     *
     * @return object
     */
    public static function findByUid($uid)
    {
        return self::where('uid', '=', $uid)->first();
    }

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

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
            while (SendingDomain::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // SendingDomain custom order
            SendingDomain::getAll()->increment('custom_order', 1);
            $item->custom_order = 0;

            // Generate dkim keys
            $item->generateDkimKeys();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'dkim_private', 'dkim_public', 'signing_enabled',
    ];

    /**
     * Get validation rules.
     *
     * @return object
     */
    public static function rules()
    {
        return [
            'name' => 'required',
            'signing_enabled' => 'required',
        ];
    }

    /**
     * Get the clean public key, strip out the Header and Footer.
     */
    public function getCleanPublicKey()
    {
        $publicKey = str_replace(array('-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----'), '', $this->dkim_public);
        $publicKey = trim(preg_replace('/\s+/', '', $publicKey));

        return $publicKey;
    }

    /**
     * Generate the Domain DNS configuration for DKIM.
     */
    public function getDnsDkimConfig()
    {
        return sprintf('%s      TXT      "v=DKIM1; k=rsa; p=%s;"', self::DKIM_FULL_SELECTOR, $this->getCleanPublicKey());
    }

    /**
     * Create the private and public key.
     *
     * @var bool
     */
    public function generateDkimKeys()
    {
        $config = array(
            'digest_alg' => 'sha256',
            'private_key_bits' => 1024,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        );

        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey['key'];

        $this->dkim_private = $privKey;
        $this->dkim_public = $pubKey;

        return true;
    }
}
