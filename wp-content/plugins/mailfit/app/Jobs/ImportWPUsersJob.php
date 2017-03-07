<?php

namespace Acelle\Jobs;

class ImportWPUsersJob extends ImportExportJob
{
    // @todo this should better be a constant
    protected $roles;
    protected $update_exists;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mailList, $user, $wp_roles, $update_exists)
    {
        // call parent's constructor
        parent::__construct($mailList, $user);
        
        // set init status
        $systemJob = $this->getSystemJob();
        $systemJob->updateData([
            "mail_list_uid" => $mailList->uid,
            "wp_roles" => json_encode($wp_roles),
            "status" => "new",
            "message" => trans("messages.starting"),
            "total" => 0,
            "success" => 0,
            "error" => 0,
            "percent" => 0
        ]);
        
        // update params
        $this->roles = $wp_roles;
        $this->update_exists = $update_exists;
    }
    
    /**
     * Get WP import roles.
     *
     * @return void
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $job = $this;
            $job->mailList->importWPUsers($job->user, $job->getSystemJob(), $job->getPath(), $job->getRoles(), $this->update_exists);
        } catch (\Exception $e) {
            $systemJob = $this->getSystemJob();
            
            // set failed
            $systemJob->updateData([
                "mail_list_uid" => $job->mailList->uid,
                "status" => "failed",
                "message" => $e->getMessage(),
                "total" => 0,
                "success" => 0,
                "error" => 0,
                "percent" => 0
            ]);
            $systemJob->save();
        }
    }
}
