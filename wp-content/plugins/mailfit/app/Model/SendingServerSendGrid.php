<?php

/**
 * SendingServerSendGrid class.
 *
 * Abstract class for SendGrid sending servers
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

use Acelle\Library\Log as MailLog;
use Acelle\Library\StringHelper;
use SendGrid\Mail;
use SendGrid\Email;
use SendGrid\Content;

class SendingServerSendGrid extends SendingServer
{
    const WEBHOOK = 'sendgrid';

    protected $table = 'sending_servers';
    public static $client = null;
    public static $isWebhookSetup = false;
    
    /**
     * Get authenticated to Mailgun and return the session object.
     *
     * @return mixed
     */
    public function client()
    {
        if (!self::$client) {
            self::$client = new \SendGrid($this->api_key);
        }
        
        return self::$client;
    }
    
    /**
     * Setup webhooks for processing bounce and feedback loop.
     *
     * @return mixed
     */
    public function setupWebhooks()
    {
        if (self::$isWebhookSetup) {
            return true;
        }
        try {
            MailLog::info('Setting up SendGrid webhooks');
            $subscribeUrl = StringHelper::joinUrl(Setting::get('url_delivery_handler'), self::WEBHOOK);
            $request_body = json_decode('{
                "bounce": true,
                "click": false,
                "deferred": false,
                "delivered": false,
                "dropped": true,
                "enabled": true,
                "group_resubscribe": false,
                "group_unsubscribe": false,
                "open": false,
                "processed": false,
                "spam_report": true,
                "unsubscribe": false,
                "url": "'.$subscribeUrl.'"
                }'
            );
            $response = $this->client()->client->user()->webhooks()->event()->settings()->patch($request_body);
            
            if($response->_status_code == '200') {
                MailLog::info('Webhooks successfully set!');
            } else {
                throw new \Exception("Cannot setup SendGrid webhooks");
            }

            self::$isWebhookSetup = true;
        } catch (\Exception $e) {
            MailLog::warning($e->getMessage());
        }
    }
    
    /**
     * Get Message Id
     * Extract the message id from SendGrid response
     *
     * @return String
     */
    public function getMessageId($headers) {
        preg_match('/(?<=X-Message-Id: ).*/', $headers, $matches);
        if (isset($matches[0])) {
            return $matches[0];
        } else {
            return NULL;
        }
    }
    
    /**
     * Prepare the email object for sending
     *
     * @return Mixed
     */
    public function prepareEmail($campaign, $subscriber, $msgId) {
        $customHeaders = $campaign->getCustomHeaders($subscriber, $this);
        // @TODO use a configurable name for Acelle Mail instead
        $customHeaders['X-Acelle-Message-Id'] = $msgId; // retain the existing message-id, overwrite the newly generated one

        $from = new Email($campaign->from_name, $campaign->from_email);
        $to = new Email(null, $subscriber->email);
        $plain = new Content("text/plain", $campaign->getPlainContent($subscriber, $msgId));
        $html = new Content("text/html", $campaign->getHtmlContent($subscriber, $msgId));
        
        $mail = new Mail($from, $campaign->getSubject($subscriber, $msgId), $to, $plain);
        foreach($customHeaders as $key => $value) {
            $mail->addHeader($key, $value);
        }
        $mail->addContent($html);
        return $mail;
    }
}
