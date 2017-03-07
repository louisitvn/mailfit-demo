<?php

/**
 * SendingServerElasticEmail class.
 *
 * Abstract class for Mailjet sending server
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
use Acelle\Model\TrackingLog;
use Acelle\Model\BounceLog;
use Acelle\Model\FeedbackLog;

class SendingServerElasticEmail extends SendingServer
{
    const WEBHOOK = 'elasticemail';
    const API_ENDPOINT = 'https://api.elasticemail.com/v2';

    protected $table = 'sending_servers';
    public static $client = null;
    public static $isWebhookSetup = false;
    public static $isCustomHeadersEnabled = false;
    
    /**
     * Get authenticated to Mailgun and return the session object.
     *
     * @return mixed
     */
    public function client()
    {
        if (!self::$client) {
            self::$client = new \ElasticEmail\ElasticEmailV2($this->api_key);
        }

        return self::$client;
    }

    /**
     * Handle notification from ElasticEmail
     * Handle Bounce/Feedback/Error
     *
     * @return mixed
     */
    public static function handleNotification($params) {
        MailLog::info('requested');

        foreach($params as $key => $value){
            MailLog::info("$key:$value");
        }

        // bounce
        if (strcasecmp($params['status'], 'Error') == 0) {
            $bounceLog = new BounceLog();
            
            // use Elastic Email transaction id as runtime-message-id
            $bounceLog->runtime_message_id = $params['transaction'];
            $trackingLog = TrackingLog::where('runtime_message_id', $bounceLog->runtime_message_id)->first();
            if ($trackingLog) {
                $bounceLog->message_id = $trackingLog->message_id;
            }

            $bounceLog->bounce_type = BounceLog::HARD;
            $bounceLog->raw = json_encode($params);
            $bounceLog->save();
            MailLog::info('Bounce recorded for message '.$bounceLog->runtime_message_id);

            // add subscriber's email to blacklist
            $subscriber = $bounceLog->findSubscriberByRuntimeMessageId();
            if ($subscriber) {
                $subscriber->sendToBlacklist($bounceLog->raw);
                MailLog::info('Email added to blacklist');
            } else {
                MailLog::warning('Cannot find associated tracking log for ElasticEmail message '.$bounceLog->runtime_message_id);
            }
        } else if (strcasecmp($params['status'], 'AbuseReport') == 0) {
            $feedbackLog = new FeedbackLog();

            // use Elastic Email transaction id as runtime-message-id
            $feedbackLog->runtime_message_id = $params['transaction'];
            
            // retrieve the associated tracking log in Acelle
            $trackingLog = TrackingLog::where('runtime_message_id', $bounceLog->runtime_message_id)->first();
            if ($trackingLog) {
                $feedbackLog->message_id = $trackingLog->message_id;
            }
            
            // ElasticEmail only notifies in case of SPAM reported
            $feedbackLog->feedback_type = 'spam';
            $feedbackLog->raw_feedback_content = json_encode($params);
            $feedbackLog->save();
            MailLog::info('Feedback recorded for message '.$feedbackLog->runtime_message_id);
            
            // update the mail list, subscriber to be marked as 'spam-reported'
            // @todo: the following lines of code should be wrapped up in one single method: $feedbackLog->markSubscriberAsSpamReported();
            $subscriber = $feedbackLog->findSubscriberByRuntimeMessageId();
            if ($subscriber) {
                $subscriber->markAsSpamReported();
                MailLog::info('Subscriber marked as spam-reported');
            } else {
                MailLog::warning('Cannot find associated tracking log for ElasticEmail message '.$feedbackLog->runtime_message_id);
            }
        }
    }

    /**
     * Enable custom headers. 
     * By default, customers headers are suppressed by Elastic Email
     *
     * @return mixed
     */
    public function enableCustomHeaders()
    {
        if (self::$isCustomHeadersEnabled) {
            return true;
        }

        try {
            $response = file_get_contents(self::API_ENDPOINT."/account/updatehttpnotification?apikey=".$this->api_key."&allowCustomHeaders=true");
            $responseJson = json_decode($response);
            
            
            if ($responseJson->success == true) {
                MailLog::info("Custom headers enabled");
                self::$isCustomHeadersEnabled = true;
            } else {
                throw new Exception("Cannot enable customer headers: ".$response);
            }
        } catch (\Exception $e) {
            MailLog::warning($e->getMessage());
        }
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
            $subscribeUrl = StringHelper::joinUrl(Setting::get('url_delivery_handler'), self::WEBHOOK);

            $response = file_get_contents(self::API_ENDPOINT."/account/updatehttpnotification?apikey=".$this->api_key."&url=".$subscribeUrl."&settings={sent:true,opened:true,clicked:true,unsubscribed:true,complaints:true,error:true}");
            if ($response == '{"success":true}') {
                MailLog::info("webhook set!");
                self::$isWebhookSetup = true;
            } else {
                throw new Exception("Cannot setup webhook. Response from server: ".$response);
            }
        } catch (\Exception $e) {
            MailLog::warning($e->getMessage());
        }
    }

    public function sendElasticEmailV2($campaign, $subscriber, $msgId) {
        $body_html = $campaign->getHtmlContent($subscriber, $msgId);
        $body_text = $campaign->getPlainContent($subscriber, $msgId);

        $customHeaders = $campaign->getCustomHeaders($subscriber, $this);
        // @TODO use a configurable name for Acelle Mail instead
        // retain the existing message-id, overwrite the newly generated one
        $customHeaders['X-Acelle-Message-Id'] = $msgId;

        // @todo: custom headers not correctly supported by Elastic Email API v2
        $result = $this->client()->email()->send([
            'to' => $subscriber->email,
            'subject' => $campaign->getSubject($subscriber, $msgId),
            'from' => $campaign->from_email,
            'fromName' => $campaign->from_name,
            'bodyHtml' => $campaign->getHtmlContent($subscriber, $msgId),
            'bodyText' => $campaign->getPlainContent($subscriber, $msgId),
            'charset' => 'utf-8',
        ]);

        $jsonResponse = json_decode($result->getData());

        // Use transactionid returned from ElasticEmail as runtime_message_id
        return $jsonResponse->data->transactionid;
    }
        
    
    /**
     * Implematation of Elastic mail/send API
     * @deprecated API v1 is supported but no longer developed
     *
     * @param mixed $campaign
     * @param mixed $subscriber
     * @param string $msgId
     *
     */
    public function sendElasticEmail($campaign, $subscriber, $msgId)
    {
        $res = "";
        $body_html = $campaign->getHtmlContent($subscriber, $msgId);
        $body_text = $campaign->getPlainContent($subscriber, $msgId);

        $customHeaders = $campaign->getCustomHeaders($subscriber, $this);
        // @TODO use a configurable name for Acelle Mail instead
        // retain the existing message-id, overwrite the newly generated one
        $customHeaders['X-Acelle-Message-Id'] = $msgId;

        $data = "api_key=".urlencode($this->api_key);
        $data .= "&from=".urlencode($campaign->from_email);
        $data .= "&from_name=".urlencode($campaign->from_name);
        $data .= "&to=".urlencode($subscriber->email);
        $data .= "&subject=".urlencode($campaign->getSubject($subscriber, $msgId));
        
        if($body_html) {
            $data .= "&body_html=".urlencode($body_html);
        }
        
        if($body_text) {
            $data .= "&body_text=".urlencode($body_text);
        }

        foreach(array_keys($customHeaders) as $i => $key) {
            $data .= "&header" . ($i+1) . "=" . $key . ": " . $customHeaders[$key];
        }

        var_dump($data);

        $header = "POST /mailer/send HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
        $fp = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);

        if(!$fp)
          throw new \Exception("Could not open connection with ssl://api.elasticemail.com");
        else {
          fputs ($fp, $header.$data);
          while (!feof($fp)) {
            $res .= fread ($fp, 1024);
          }
          fclose($fp);
        }

        // extract the transaction ID from socket responses
        // which is something like: f74b9f96-f89a-4cfe-813f-5f86df1cb37f
        preg_match('/[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/', $res, $matches);

        if (array_key_exists(0, $matches)) {
          return $matches[0];
        } else {
          throw new \Exception("Sending failed: \n".$res);
        }
    }
}
