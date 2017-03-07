<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Acelle\Library\QuotaTrackerRedis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class QuotaTrackerRedisTest extends TestCase
{
    /**
     * Test if the quota tracking system itself is correct
     *
     * @return void
     */
    public function testOverQuota()
    {
        /**
         * Suppose the quota setting is: {6 emails, every 5 seconds}
         * Then we test with: send an email every 1 second and and expect it will NOT to exceed the quota
         */
        $tracker = new QuotaTrackerRedis(Redis::connection(), '5 seconds', 6);
        $tracker->init();
        $result = $this->nTimes(10, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });
        $this->assertTrue($result);
        
        /**
         * Suppose the quota setting is: {5 emails, every 4 seconds}
         * Then we test with: send an email every 1 second and and expect it will exceed the quota
         */
        $tracker = new QuotaTrackerRedis(Redis::connection(), '5 second', 4);
        $tracker->init();
        $result = $this->nTimes(5, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });
        $this->assertFalse($result);

        /**
         * Suppose the quota setting is: {0 emails, every 1 hours}
         * Then we test with: send an email every 1 second and and expect it to fail immediately at first try
         */
        $tracker = new QuotaTrackerRedis(Redis::connection(), '1 hour', 0);
        $tracker->init();
        $result = $this->nTimes(1, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });
        $this->assertFalse($result);

        /**
         * Suppose the quota setting is: {1 emails, every 1 day}
         * Then we test with: send an email every 1 second and and expect it NOT to fail after 1st try
         */
        $tracker = new QuotaTrackerRedis(Redis::connection(), '1 days', 1);
        $tracker->init();
        $result = $this->nTimes(1, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });
        $this->assertTrue($result);

        /**
         * Suppose the quota setting is: {1 emails, every 1 day}
         * Then we test with: send an email every 1 second and and expect it to fail after 2nd try
         */
        $tracker = new QuotaTrackerRedis(Redis::connection(), '1 days', 1);
        $tracker->init();
        $result = $this->nTimes(2, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });
        $this->assertFalse($result);
    }
    
    /**
     * Simulate an activity every second
     *
     * @return void
     */
    private function nTimes($try, $tracker, $func) {
        $success = true;
        for($i = 0; $i < $try; $i++) {
            $success = $func($tracker);
            if (!$success) {
                return false;
            }
            sleep(1);
        }
        return $success;
    }
}
