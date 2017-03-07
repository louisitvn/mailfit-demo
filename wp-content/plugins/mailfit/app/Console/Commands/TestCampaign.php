<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;
use Acelle\Library\Log;
use Acelle\Library\QuotaTrackerStd;
use Acelle\Library\QuotaTrackerRedis;
use Acelle\Library\StringHelper;
use Acelle\Library\QuotaTracker;
use Acelle\Model\Campaign;
use Acelle\Model\User;
use Acelle\Model\MailList;
use Acelle\Model\Subscriber;
use Acelle\Model\TrackingLog;
use Acelle\Model\AutoEvent;
use Acelle\Model\SendingServer;
use Acelle\Model\AutoTrigger;
use Acelle\Model\SendingServerElasticEmailApi;
use Acelle\Model\SendingServerElasticEmail;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

class TestCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::first();
        echo $user->getSendingQuota() . "/" . $user->getQuotaIntervalString() . "\n";
        $user->getQuotaTracker()->add();
        $user->saveQuotaUsageInfo();

        $user->quotaDebug();

        $e = AutoEvent::find(2);
        $e->check();
    }

    public function testSmtp() {
        $transport = \Swift_SmtpTransport::newInstance('smtp.elasticemail.com', 2525, 'tls')
          ->setUsername('asish@tsjl.in')
          ->setPassword('266449d5-8c3f-4e9c-bb80-12666bf16dcb')
          ;

        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);

        // Create a message
        $message = \Swift_Message::newInstance('Wonderful Subject')
          ->setFrom(array('asish@tsjl.in' => 'Asish'))
          ->setTo(array('louisitvn@gmail.com' => 'Louis'))
          ->setBody('Here is the message itself')
          ;

        // Send the message
        $result = $mailer->send($message);

        var_dump($result);
    }
}

